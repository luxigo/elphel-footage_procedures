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
            <fieldset id="step_download" class="panel">
                <legend>Download footage and logs</legend>
                
                <div class="parameters">
                    <table>
                        <tr>
                            <td><label for="cameras_folder">Destination folder :</label></td>
                            <td><input id="cameras_folder" type="text" class="file" value="/data/camera/"></td>
                            <td><button class="browse" directory>...</button></td>
                        </tr>
                        <tr>
                            <td><label for="cam_erase">Erase from camera :</label></td>
                            <td><input id="cam_erase" type="checkbox"></td>
                        </tr>
                        <tr>
                            <td><label for="chain_upto">Goal :</label></td>
                            <td>
                                <select id="chain_upto">
                                  <option>-</option>
                                  <option>JP4</option>
                                  <option>Equirectangular</option>
                                  <option>Equirectangular stitched</option>
                                  <option>Equirectangular WebGL tiles</option>
                                  <option>Gnomonic</option>
                                  <option>Point cloud</option>
                                </select> 
                                <button>+</button>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="parallelize">Parallellize :</label></td>
                            <td><table cellspacing=0><tr><td><input id="parallelize" type="checkbox"></td><td><button id="parallel_settings">Settings</button></td></tr></table></td>
                        </tr>
                    </table>
                </div>
                <button class="go right">Start</button>
            </fieldset>

            <fieldset id="step_extract" class="panel">
                <legend>Extract JP4 images</legend>
                
                <div class="parameters">
                    <table>
                        <tr>
                            <td><label for="mov_folder">Source folder :</label></td>
                            <td><input id="mov_folder" type="text" class="file"></td>
                            <td><button class="browse" directory>...</button></td>
                        </tr>
                        <tr>
                            <td><label for="jp4_folder">Destination folder :</label></td>
                            <td><input id="jp4_folder" type="text" class="file"></td>
                            <td><button class="browse" directory>...</button></td>
                        </tr>
                        <tr>
                            <td><label for="chain_upto">Goal :</label></td>
                            <td>
                                <select id="chain_upto">
                                  <option>JP4</option>
                                  <option>Equirectangular</option>
                                  <option>Equirectangular stitched</option>
                                  <option>Equirectangular WebGL tiles</option>
                                  <option>Gnomonic</option>
                                  <option>Point cloud</option>
                                </select>
                                <button>+</button>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="parallelize">Parallellize :</label></td>
                            <td><table cellspacing=0><tr><td><input id="parallelize" type="checkbox"></td><td><button id="parallel_settings">Settings</button></td></tr></table></td>
                        </tr>
                    </table>
                </div>
                 <div class="right"><button id="mov_split" class="go" enabled>Start</button></div>
            </fieldset>
            <fieldset id="step_postproc" class="panel">
                <legend>Post processing</legend>
                <div class="parameters">
                    <table>
                        <tr>
                            <td><label for="xml_prefs">Post-processing preferences :</label></td>
                            <td><input id="xml_prefs" type="text" class="file"></td>
                            <td><button class="browse">...</button></td>
                            <td><button id="xml_prefs_edit" disabed>Edit</button></td>
                        </tr>
                        <tr>
                            <td><label for="imagej_processed_folder">Destination folder :</label></td>
                            <td><input id="imagej_processed_folder" type="text" class="file"></td>
                            <td><button class="browse" directory>...</button></td>
                        </tr>
                        <tr>
                            <td><label for="chain_upto">Goal :</label></td>
                            <td>
                                <select id="chain_upto">
                                  <option>Equirectangular</option>
                                  <option>Equirectangular stitched</option>
                                  <option>Equirectangular WebGL tiles</option>
                                  <option>Gnomonic</option>
                                  <option>Point cloud</option>
                                </select>
                                <button>+</button>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="parallelize">Parallellize :</label></td>
                            <td><table cellspacing=0><tr><td><input id="parallelize" type="checkbox"></td><td><button id="parallel_settings">Settings</button></td></tr></table></td>
                        </tr>
                    </table>
                </div>
                <div class="right"><button id="post_process" class="go">Start</button></div>
            </fieldset>
                        </tr>
                        <tr>
                            <td><label for="pano_folder">Stitched panoramas folder :</label></td>
                            <td><input id="pano_folder" type="text" class="file"></td>
                            <td><button class="browse" directory>...</button></td>
                        </tr>
                    </table>
                </div>
            </fieldset>

                <div id=buttons>
                    <table>
                        <tr><td style="padding:3px;"><button class=button id=splitall onclick="splitall()">Split All *.movs</button></td></tr>
                        <tr><td style="padding:3px;"><button class=button id=filter onclick="filter()">Filter Out Images with Non-Matching Timestamps</button></td></tr>
                        <tr><td style="padding:3px;"><button class=button id=kml_gen onclick="kml_gen()">Generate KML</button></td></tr>
                        <tr><td style="padding:20px 0px;"><button class=button id=copy_all onclick="copy_all()">Copy All</button>&nbsp;to <b>/data/post-processing/src</b>&nbsp;&nbsp;OR use&nbsp;&nbsp;<a href='../panorama_preview/'>Eyesis Panorama Previewer</a></td></tr>
                    </table>
                </div>
                <div>
                    <button class=button id=step1_run_all onclick="step1_run_all()">Run All</button>
                </div>
            </fieldset>
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
                <div>
                    <button class=button id=step1_run_all onclick="step3_run_all()">Run All</button>
                </div>
            </div>

            <div id=status style="width:700px;">Status: <span id=status_span>Idle</span><span id=blinking_span></span></div>

     </body>
</html>
