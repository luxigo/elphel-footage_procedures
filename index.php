<!DOCTYPE html>
<html>
    <head>
        <title>Eyesis Footage Procedures</title>
        <script src="js/jquery.js"></script>
        <script src="js/jquery-ui.js"></script>
        <script src="js/jquery.cookie.js"></script>
        <script src="js/jqueryFileTree.js"></script>
        <script src="js/footage_procedures.js"></script>
        <link rel="some icon" href="img/fp_logo.png">
        <link rel="stylesheet" type="text/css" href="css/jqueryFileTree.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery-structure.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery-theme.css" />
        <link rel="stylesheet" type="text/css" href="css/footage_procedures.css" />
     </head>
     <body>
            <h4>Step 1: Extracting, creating KML, Previewing, Copying</h4>
            <div id=step1_div class="panel">
                <div id=folders>
                    <table>
                        <tr>
                            <td>Processing folder:</td>
                            <td><input id="processing_folder" type="text"></td>
                            <td><button class="browse" directory>...</button></td>
                        </tr>
                    </table>
                </div>
                <div id=buttons>
                    <table>
                        <tr><td style="padding:3px;"><button class=button id=splitall onclick="splitall()">Split All *.movs</button></td></tr>
                        <tr><td style="padding:3px;"><button class=button id=filter onclick="filter()">Filter Out Images with Non-Matching Timestamps</button></td></tr>
                        <tr><td style="padding:3px;"><button class=button id=kml_gen onclick="kml_gen()">Generate KML</button></td></tr>
                        <tr><td style="padding:20px 0px;"><button class=button id=copy_all onclick="copy_all()">Copy All</button>&nbsp;to <b>/data/post-processing/src</b>&nbsp;&nbsp;OR use&nbsp;&nbsp;<a href='../panorama_preview/'>Eyesis Panorama Previewer</a></td></tr>
                    </table>
                </div>
                <div style="position:absolute;top:270px;left:750px;">
                    <button class=button id=step1_run_all onclick="step1_run_all()">Run All</button>
                </div>
            </div>
            <h4>Step 2: Enhancing in ImageJ</h4>    
            <div id=step2_div class="panel">
                <div id=step2_folders>
                    <table>
                        <tr>
                            <td>Source folder:</td>
                            <td><input id="s2_sf" type=text style="width:300px" value='/data/footage/test' /></td>
                        </tr>
                        <tr>
                            <td>Results folder:</td>
                            <td><input id="s2_rf" type=text style="width:300px" value='/data/post-processing' /></td>
                        </tr>
                        <tr>
                            <td>ImageJ-Eyesis correction parameters:</td>
                            <td><input id="s2_cp" type=text style="width:300px" value='/data/footage/Eyesis_Correction.xml' /></td>
                        </tr>
                    </table>
                </div>
                <div style="position: relative; left:737px; margin-bottom: 10px;">
                    <button class=button id=step2_run_all onclick="step2_run_all()">Run All</button>
                </div>
            </div>
            <h4>Step 3: Stitching, splitting for WebGL Editor</h4>    
            <div id=step3_div class="panel">
                <div id=step3_folders>
                    <table>
                        <tr>
                            <td>Processing root folder:</td>
                            <td><input id="s3_pf" type=text style="width:300px" value='/data/post-processing' /></td>
                        </tr>
                        <tr>
                            <td>Sources subfolder:</td>
                            <td><input id="s3_src_sub" type=text width=200 value='src' /></td>
                        </tr>
                        <tr>
                            <td>ImageJ-processed subfolder:</td>
                            <td><input id="s3_processed_sub" type=text width=200 value='imagej_processed' /></td>
                        </tr>
                        <tr title="Used when processed tiff from ImageJ is converted into jpeg">
                            <td>Black Point, %:</td>
                            <td><input type="text" id="s3_bp" value="0" /></td>
                        </tr>
                        <tr title="Used when processed tiff from ImageJ is converted into jpeg -> for 32-bit tiff - 25%, for 16-bit - 50%, for 8-bit - 100%">
                            <td>White Point, %:</td>
                            <td><input type="text" id="s3_wp" value="25" /></td>
                        </tr>
                        <tr title="Used when processed tiff from ImageJ is converted into jpeg">
                            <td>Compression Quality, %:</td>
                            <td><input type="text" id="s3_cq" value="95" /></td>
                        </tr>
                    </table>
                </div>
                <div id=step3_buttons>
                    <table>
                        <tr>
                            <td style="padding:3px;"><button class=button id=step3_stitch onclick="step3_stitch()">Stitch</button></td>
                        </tr>
                        <tr><td style="padding:3px;"><button class=button id=Step3_cut onclick="step3_split()">Split images for WebGL Editor</button></td></tr>
                        <tr><td style="padding:3px;"><button class=button id=step3_compress onclick="step3_compress()">Compress images for Google Earth</button></td></tr>
                    </table>
                </div>
                <br/>
                <div id=step3_kml_gen_div>
                    <table>
                        <tr><td>Path prefix inside KML:   </td><td><input id="s3_dest" type=text style="width:500px" value='http://127.0.0.1/panoramas' /></td></tr>
                        <tr><td>Visibility:   </td><td><input id="s3_visibility" type=text style="width:50px" value='1' /></td></tr>
                        <tr><td>Staring Index:</td><td><input id="s3_index" type=text style="width:50px" value='1' /></td></tr>
                    </table>

                    <table style="position:relative;left:20px;">
                        <tr><td style="padding:3px;"><button class=button id=step3_kml_gen onclick="step3_kml()">Generate KML for WebGL Editor</button></td></tr>
                    </table>
                </div>
                <div style="position:absolute;top:765px;left:750px;">
                    <button class=button id=step1_run_all onclick="step3_run_all()">Run All</button>
                </div>
            </div>

            <div id=status style="width:700px;">Status: <span id=status_span>Idle</span><span id=blinking_span></span></div>

     </body>
</html>
