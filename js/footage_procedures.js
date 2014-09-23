var selected_fileDialogItemp;

$(document).ready(function(){

  // restore inputs, set save on change
  input_init();

  // jquery fileTree dialog for buttons of class "browse"
  $("button.browse").on('click.browse',function(e){
    var input=$('input',$(e.target).closest('tr'));
    var div=$('#openFileDialog_'+input.attr('id'));
    if (!div.length) {
      div=$('<div id="openFileDialog_'+input.attr('id')+'">');
      div.fileTree({
        root: 'data',
        loadMessage: 'Loading...',
        folderEvent: 'mouseup',
        showFiles: $(e.target).attr('directory')==undefined
      }, function(file) {
        var callback=$(e.target).data('callback');
        if (typeof(callback)=="function") {
          callback(e.target,file);
        } else {
          var input=$(e.target).prev('input');
          input.val(file);
        }
      });
      div.wrap('<div class="wrap openFileDialog">');
      div.on('filetreeclicked filetreeexpand',function(e){
        selected_dialogItem=e.target;
      });
    }
    div.dialog({
      modal: true,
      maxHeight: $('body').height(),
      open: function(){
        $(document).on('keyup.fileTree',function(e){
          console.log(e.keyCode);
          if (e.keyCode==13) {
            if (selected_dialogItem) {
              div.dialog('close');
              input.val($(selected_dialogItem).attr('rel')).change();
            }
          }
        });
      },
      close: function() {
        $(document).off('keyup.fileTree');
      }
    });
  });

  $('button#xml_prefs_edit').on('click',function(){
    var iframe=$('#xml_prefs_editor');
    var xml='..'+$('#xml_prefs').val();
    if (iframe.length) {
      iframe.closest('div').remove();
    }
    iframe=$('<iframe>');
    iframe.on('load',function(){
      $(window).trigger('resize.xml_prefs');
    });
    var div=$('#xml_prefs_editor_wrapper');
    if (!div.length) {
      div=$('<div id="xml_prefs_editor_wrapper"></div>');
      div.append(iframe);
    }
    $('body').css('overflow','hidden');
    $(div).dialog({
      modal: true,
      position: {
        my: "top",
        at: "top"
      },
      heigth: $('body').height(),
      width: $('body').width(),
      open: function() {
        $(window).on('resize.xml_prefs',function(){
          var height=$(window).prop('innerHeight');
          var width=$(window).prop('innerWidth');
          console.log(width+'x'+height);
          div.width(width);
          div.height(height);
          iframe.width(width-16);
          iframe.height(height);
          console.log(iframe.closest('.ui-dialog'));
          iframe.closest('.ui-dialog').width(width-16);
          iframe.closest('.ui-dialog').height(height);
        }).resize();
      },
      close: function() {
        $('body').css('overflow','');
        $(window).off('resize.xml_prefs');
      },
      draggable: false
      
    });
    var timestamp=new Date().getTime();
    iframe.attr('src','imagej-elphel-config-editor/index.html?exclude=sourcePath|sourceDirectory|resultsDirectory&xml='+xml+'&include=CORRECTION_PARAMETERS&expand=true&t='+timestamp+'">');
  });
});

function input_init() {
  $('input').each(function(){
    $(this)
    .val($.cookie(this.id))
    .change(function(e){
      $.cookie(e.target.id,$('input#'+e.target.id).val());
    });
  });
}

var working_timeout;

function splitall(){
  request = "split_mov_customized.php?ext=jp4&path="+$("#mov_folder").val();
  $("#status_span").html("Splitting");
  ajax_request(request,"Splitting done.");
}

function filter(){
  request = "filter_jp4s.php?ext=jp4&path="+$("#mov_folder").val();
  $("#status_span").html("Filtering");
  ajax_request_async(request,"Filtering done.");
}

function kml_gen(){
  request = "exif2kml_local.php?ext=jp4&path="+$("#mov_folder").val();
  $("#status_span").html("Generating KML");
  ajax_request_async(request,"KML generated.");
}

function copy_all(){
  request = "copy_all.php?src=/data/footage/"+$("#mov_folder").val()+"&dest="+$("#jp4_folder")+"&imagej="+$("#imagej_processed_folder");
  $("#status_span").html("Copying");
  ajax_request(request,"Copying done.");
}

function step3_stitch(){
  request = "stitch.php?dest="+$("#s3_pf").val()+"/results"+"&src="+$("#s3_pf").val()+"/"+$("#s3_processed_sub").val()+"&bp="+$("#s3_bp").val()+"&wp="+$("#s3_wp").val()+"&q="+$("#s3_cq").val();
  $("#status_span").html("Stitching");
  ajax_request_async(request,"Stitching done.");
}

function step3_split(){
  request = "prepare_images_for_wpe.php?path="+$("#s3_pf").val()+"/results";
  $("#status_span").html("Splitting for WPE");
  ajax_request_async(request,"Splitting for WPE done.");
}

function step3_compress(){
  request = "prepare_images_for_google_earth.php?path="+$("#s3_pf").val()+"/results";
  $("#status_span").html("Compressing for GE");
  ajax_request(request,"Compressing for GE done.");
}

function step3_kml(){
  request = "exif2kml.php?ext=jp4&path="+$("#s3_pf").val()+"/"+$("#s3_src_sub").val()+"&visibility="+$("#s3_visibility").val()+"&index="+$("#s3_index").val()+"&dest="+$("#s3_dest").val();
  $("#status_span").html("Generating KML");
  ajax_request(request,"Generating KML done.");
}

function step1_run_all() {
  splitall();
  filter();
  kml_gen();
}

function step2_run_all() {
  working_timeout = setTimeout(working,1000);
  $.ajax({
    url: "run_imagej-elphel_eyesis-correction.php",
    data: {
      source: $('#s2_sf').val(),
      results: $('#s2_rf').val(),
      prefs: $('#s2_cp').val()
    },
    success: function(data,textStatus,jqXHR) {
      if (data.error) {
        procedure_done(data.error);
      } else {
        $('#status_span',html(data.message));
          show_progress('Eyesis_Correction',data.timestamp);
      }
    },
    error: function(jqXHR,textStatus,errorThrown) {
      procedure_done(textStatus+' '+errorThrown);
    }
  });
}

function step3_run_all() {
  step3_stitch();
  step3_split();
  step3_compress();
  step3_kml();
}

function ajax_request(request,done_message){
  working_timeout = setTimeout(working,1000);
  $.ajax({
    url: request,
    async: true,
    success: function(){
      //splitting_done();
      procedure_done(done_message);
    }
  });
}

function ajax_request_async(request,done_message){
  working_timeout = setTimeout(working,1000);
  $.ajax({
    url: request,
    async: true,
    success: function(){
      //splitting_done();
      procedure_done(done_message);
    }
  });
}

function procedure_done(message){
  clearTimeout(working_timeout);
  $("#blinking_span").html("");
  $("#status_span").html(message);
}

function working(){
  var d = new Date();

  var curr_sec = d.getSeconds();

  if      (curr_sec%4==1) $("#blinking_span").html(".");
  else if (curr_sec%4==2) $("#blinking_span").html("..");
  else if (curr_sec%4==3) $("#blinking_span").html("...");
  else                    $("#blinking_span").html("");

  working_timeout=setTimeout(working,1000);
}

function show_progress(jobname,timestamp) {
  $.ajax({
    url: "progress.php",
    data: {
      j: jobname,
      t: timestamp
    },
    success: function(reply) {
      switch(reply.status) {
      case 'running':
        $('#status_span').html(reply.command+' running ('+reply.elapsed.toHHMMSS()+')');
        setTimeout(function(){
          show_progress(jobname,timestamp);
        },10000);
        break;
      case 'terminated':
        procedure_done('Job terminated after '+reply.elapsed.toHHMMSS()+' with exit code '+reply.exit_code);
        break; 
      case 'error':
        procedure_done('Error: '+reply.error);
        break;
      }
    },
    error: function(jqXHR,textStatus,errorThrown) {
      $('#status_span').html('Server error: '+textStatus+' '+errorThrown);
      console.log(arguments);
      setTimeout(function(){
        show_progress(jobname,timestamp);
      },10000);
    }
  });
}

String.prototype.toHHMMSS = function () {
  var sec_num = parseInt(this, 10); // don't forget the second param
  var hours   = Math.floor(sec_num / 3600);
  var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
  var seconds = sec_num - (hours * 3600) - (minutes * 60);

  if (hours   < 10) {hours   = "0"+hours;}
  if (minutes < 10) {minutes = "0"+minutes;}
  if (seconds < 10) {seconds = "0"+seconds;}
  var time    = hours+':'+minutes+':'+seconds;
  return time;
}
