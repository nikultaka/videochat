<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo plugins_url('website-custom-plugin/WCP/assets/js/fabric.js'); ?>"></script> 
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('website-custom-plugin/WCP/assets/css/style.css'); ?>" >    
<script type='text/javascript' src='https://cdn.scaledrone.com/scaledrone.min.js'></script>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">   
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script> 

<script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
<!-- <script type="text/javascript" src="https://hootenanny-dev.serverdatahost.com/assets/simplewebrtc.bundle.js"></script> -->
<script type="text/javascript" src="<?php echo plugins_url('website-custom-plugin/WCP/assets/js/simplewebrtc.bundle.js'); ?>"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js" ></script>        

<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<style>  
    .site-header, .header-footer-group  { display: none; }
    .section-inner { display: none; }    
    .squareColorBox {
      width: 26px;
      height: 10px;
      border : 1px solid black;
      display: inline-block;
    }
    .swall-overlay {
        z-index: 100005;   
    }
    .swal-modal {
        z-index: 99999;
    }
    .box-dice {
      z-index: 9 !important;
    }
</style>      

<?php
$flatBlueURL = plugins_url('website-custom-plugin/WCP/assets/images/flat-blu-010.png');
$flatGrnURL = plugins_url('website-custom-plugin/WCP/assets/images/flat-grn-010.png');
$flatRedURL = plugins_url('website-custom-plugin/WCP/assets/images/flat-red-010.png');
$flatYelURL = plugins_url('website-custom-plugin/WCP/assets/images/flat-yel-010.png');
$doneURL = plugins_url('website-custom-plugin/WCP/assets/images/done.png');
$noImageURL = plugins_url('website-custom-plugin/WCP/assets/images/blank_profile_picture.png');             

$yellow_zero_left_position = 112;
$yellow_zero_top_position = 6;    

$yellow_one_left_position = 112;
$yellow_one_top_position = 56;

$yellow_two_left_position = 112;
$yellow_two_top_position = 106;

$yellow_three_left_position = 112;
$yellow_three_top_position = 156;

$blue_zero_left_position = 1246;
$blue_zero_top_position = 5;

$blue_one_left_position = 1246;
$blue_one_top_position = 55;

$blue_two_left_position = 1246;
$blue_two_top_position = 105;

$blue_three_left_position = 1246;
$blue_three_top_position = 155;

$red_zero_left_position = 1246;
$red_zero_top_position = 576;

$red_one_left_position = 1246;
$red_one_top_position = 526;

$red_two_left_position = 1246;
$red_two_top_position = 476;

$red_three_left_position = 1246;
$red_three_top_position = 426;

$green_zero_left_position = 111;
$green_zero_top_position = 576;

$green_one_left_position = 111;
$green_one_top_position = 526;

$green_two_left_position = 111;
$green_two_top_position = 476;

$green_three_left_position = 111;
$green_three_top_position = 426;
 
$user_id = get_current_user_id();   

$is_admin = 0;
if(!empty($roomData)) {
    foreach($roomData as $key => $value) {
        $is_admin = $value->is_admin;
        $room_admin_user_id = $value->user_id;  
        if( ($room_admin_user_id == $user_id) && $is_admin == "1") {
            $is_admin = "1";          
        }
    }
}
$user_one_color = "#FFC000";
$user_two_color = "#305496";
$user_three_color = "#A9C099";
$user_four_color = "#DE7E7E";
$room_id = '';

if(isset($_GET['id']) && $_GET['id']!='') {
  $room_id = $_GET['id'];  
} else {
  wp_redirect('create-room');
  exit;  
}    

?> 

<div id="reset_game" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reset Game</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure want to reset game ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="resetMarble();">Ok</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="mymodal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Set Color</h2>  
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>  
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
              &nbsp;
          </div>
          <div class="col-md-2">
            <span class="squareColorBox" style="background: yellow;"></span>
          </div>
          <div class="col-md-2">
            <span class="squareColorBox" style="background: blue;"></span>
          </div>
          <div class="col-md-2">
            <span class="squareColorBox" style="background: red;"></span>
          </div>
          <div class="col-md-2">
            <span class="squareColorBox" style="background: green;"></span>
          </div>  
        </div>  
        <form>

            <div class="row">
              <div class="col-md-4">
                  User One
              </div>  
              <div class="col-md-2">
                <span class="squareColorBox" id="one_yellow" onclick="userColor('one','yellow');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="one_blue" onclick="userColor('one','blue');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="one_red" onclick="userColor('one','red');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="one_green" onclick="userColor('one','green');"></span>
              </div>  
            </div>

            <div class="row">
              <div class="col-md-4">
                  User Two
              </div>  
              <div class="col-md-2">
                <span class="squareColorBox" id="two_yellow" onclick="userColor('two','yellow');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="two_blue" onclick="userColor('two','blue');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="two_red" onclick="userColor('two','red');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="two_green" onclick="userColor('two','green');"></span>
              </div>  
            </div>

            <div class="row">
              <div class="col-md-4">
                  User Three
              </div>  
              <div class="col-md-2">
                <span class="squareColorBox" id="three_yellow" onclick="userColor('three','yellow');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="three_blue" onclick="userColor('three','blue');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox"  id="three_red" onclick="userColor('three','red');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox"  id="three_green" onclick="userColor('three','green');"></span>
              </div>  
            </div>

            <div class="row">
              <div class="col-md-4">
                  User Four
              </div>  
              <div class="col-md-2">
                <span class="squareColorBox" id="four_yellow" onclick="userColor('four','yellow');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="four_blue" onclick="userColor('four','blue');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="four_red" onclick="userColor('four','red');"></span>
              </div>
              <div class="col-md-2">
                <span class="squareColorBox" id="four_green" onclick="userColor('four','green');"></span>
              </div>  
            </div>    


          
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="saveUserColor();">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="resetGame()" >Reset Game</button>
      </div>  
    </div>
  </div>
</div>


<?php if($is_admin == "1") { ?>
  <button type="button" class="btn btn-primary" style="position: absolute;top:2;left: 0;z-index: 9999" onclick="setColor()" >Set Color</button>
<?php } ?>               

<div id="dice" class="box-dice" data-side="1" style="position: absolute;top: 0; left: 2%; bottom: 9%; right: 14%;margin: auto;z-index: 9999;">      
  <!-- display: none; -->
        <div class="sides side-1">
            <span class="dot dot-1"></span>
        </div>      
        <div class="sides side-2">
            <span class="dot dot-1"></span>
            <span class="dot dot-2"></span>
        </div>
        <div class="sides side-3">
            <span class="dot dot-1"></span>
            <span class="dot dot-2"></span>  
            <span class="dot dot-3"></span>
        </div>
        <div class="sides side-4">
            <span class="dot dot-1"></span>
            <span class="dot dot-2"></span>  
            <span class="dot dot-3"></span>
            <span class="dot dot-4"></span>
        </div>
        <div class="sides side-5">
            <span class="dot dot-1"></span>
            <span class="dot dot-2"></span>  
            <span class="dot dot-3"></span>
            <span class="dot dot-4"></span>
            <span class="dot dot-5"></span>
        </div>
        <div class="sides side-6">
            <span class="dot dot-1"></span>
            <span class="dot dot-2"></span>  
            <span class="dot dot-3"></span>
            <span class="dot dot-4"></span>
            <span class="dot dot-5"></span>
            <span class="dot dot-6"></span>
        </div>
    </div>
 
      
    <video id="localVideo" width="390" height="390" style="height: auto;display: none" autoplay muted></video>     
    <video id="remoteVideo1" width="440" height="390" style="height: auto;display: none" autoplay></video>    
    <video id="remoteVideo2" width="390" height="390" style="height: auto;display: none" autoplay></video>  
    <video id="remoteVideo3" width="390" height="390" style="display: none" autoplay></video>     
    <input type="hidden" id="remote1" value="" />
    <input type="hidden" id="remote2" value="" />
    <input type="hidden" id="remote3" value="" />
    <input type="hidden" id="current_turn" value="<?php if($is_admin == "1") { echo $user_id; } ?>" /> 
    <input type="hidden" id="turn_user_id" value="">
    <input type="hidden" id="dice_result" value="">
    <canvas id="c" style="border: 1px solid black; position: relative;"></canvas>                 
            
    <script>

     var videoScaleData = Array(); 
     var canvas;
     var video1,remoteVideo1,remoteVideo2,remoteVideo3;
     var video1El,remoteVideo1El,remoteVideo2El,remoteVideo3El;
     var remoteStream1 = '';
     var remoteStream2 = '';
     var remoteStream3 = '';

    
    (function() {      

            canvas = this.__canvas = new fabric.Canvas('c',{ selection: false });

            video1El = document.getElementById('localVideo');
            remoteVideo1El = document.getElementById('remoteVideo1');
            remoteVideo2El = document.getElementById('remoteVideo2');
            remoteVideo3El = document.getElementById('remoteVideo3');  

            video1 = new fabric.Image(video1El, {        
                  left: 262, //310 0  262     
                  top: 0,        
                  flipX: true,  
                  cropX:100,  
                  //cropY:50,    
                  originX: 'center',      
                  originY: 'center',      
                  lockMovementX:true,
                  lockMovementY:true,
                  objectCaching: false, 
                  id: 'localCanvasVideo',
            });  
            canvas.add(video1);
            //canvas.moveTo(video1,1);
            
            

            

            remoteVideo1 = new fabric.Image(remoteVideo1El, {        
                  left: 996,  
                  top: 0,  
                  flipX: true,  
                  cropX:150,  
                  originX: 'center',
                  originY: 'center',
                  lockMovementX:true,
                  lockMovementY:true,
                  id: 'remoteCanvasVideo1'
            });  
            canvas.add(remoteVideo1);      

            remoteVideo2 = new fabric.Image(remoteVideo2El, {        
                  left: 255,  
                  top: 610,  
                  flipX: true,  
                  cropY:200,    
                  cropX:110,           
                  originX: 'center',
                  originY: 'center',
                  lockMovementX:true,
                  lockMovementY:true,
                  id: 'remoteCanvasVideo2'  
            });  
            canvas.add(remoteVideo2);      

            remoteVideo3 = new fabric.Image(remoteVideo3El, {        
                  left: 1016,      
                  top: 610,  
                  flipX: true,  
                  cropY:200,  
                  cropX:110,           
                  originX: 'center',
                  originY: 'center',
                  lockMovementX:true,
                  lockMovementY:true,
                  objectCaching: false, 
                  id: 'remoteCanvasVideo3'
            });  
            canvas.add(remoteVideo3);         

            /*************** video streaming *************/
            const roomHash =  "123";
            const drone = new ScaleDrone('2xmbUiTsqTzukyf7');     
            const roomName = 'observable-' + roomHash;
            const configuration = {
              iceServers: [{
                  urls: 'stun:stun.l.google.com:19302'
              }]
            };
            let room;
            let pc;
            let pc1;  
            let remoteStream = new MediaStream();
            var localstreaming;
            var myPeerID = '';



            function onSuccess() {};
            function onError(error) {
              console.error(error);
            }; 

            drone.on('open', error => {
              if (error) {
                return console.error(error);
              }
              room = drone.subscribe(roomName);
              room.on('open', error => {
                if (error) {
                  onError(error);
                }
              });  

              room.on('member_leave', function(member) {
                  console.log("member leave");
                  console.log(member);  
              });       
              room.on('members', members => {    
                console.log('MEMBERS', members);
                var client_id = drone.clientId;   
                videoScaleData.push({"video1":client_id}); 
                const isOfferer = members.length === 2;
                startWebRTC(isOfferer);
              });
            });

            // Send signaling data via Scaledrone
            function sendMessage(message) {
              drone.publish({
                room: roomName,
                message    
              });
            }

            function startWebRTC(isOfferer) {
              pc = new RTCPeerConnection(configuration);
              var dc = pc.createDataChannel("my channel");

              // 'onicecandidate' notifies us whenever an ICE agent needs to deliver a
              // message to the other peer through the signaling server
              pc.onicecandidate = event => {
                if (event.candidate) {
                  sendMessage({'candidate': event.candidate});
                }
              };

              // If user is offerer let the 'negotiationneeded' event create the offer
              if (isOfferer) {
                pc.onnegotiationneeded = () => { 
                  pc.createOffer().then(localDescCreated).catch(onError);
                }
              }

    
              pc.onaddstream = (remoteaddstream) => { 
                    /*console.log(remoteaddstream);     
                    if(remoteStream1 == '') {
                        remoteStream1stream = remoteaddstream.stream;
                        var remotestreamid = remoteStream1stream.id;
                        console.log("remote stream id:"+remotestreamid);  
                        addUser(remotestreamid,remoteStream1stream,"remote",'');   
                    }*/
              };

              /*navigator.mediaDevices.getUserMedia({
                audio: true,
                video: true,
              }).then(stream => {
                var localstreamid = stream.id;
                console.log("stream id :"+localstreamid);          
                pc.addStream(stream);
                addUser(localstreamid,stream,"local",drone.clientId);           
              }, function(e) { 
                  addUser('','',"local",drone.clientId);           
                  console.log(e);     
              });*/


              /*navigator.mediaDevices.getUserMedia({
                audio: true,
                video: true,
              }).then(stream => {
                console.log("stream");
                console.log(stream);
                var localstreamid = stream.id;
                console.log("stream id :"+localstreamid);          
                localstreaming = stream;
                myPeerID = webrtc.connection.getSessionid();
                addUser(localstreamid,stream,"local",myPeerID);           
              }, function(e) { 
                //addUser('','',"local",myPeerID);           
                console.log(e);     
              });*/
              

              // Listen to signaling data from Scaledrone
              room.on('data', (message, client) => {
                // Message was sent by us
                if (client.id === drone.clientId) {  
                  return;
                }

                if (message.sdp) {
                  // This is called after receiving an offer or answer from another peer
                  pc.setRemoteDescription(new RTCSessionDescription(message.sdp), () => {
                    // When receiving an offer lets answer it
                    if (pc.remoteDescription.type === 'offer') {
                      pc.createAnswer().then(localDescCreated).catch(onError);
                    }
                  }, onError);
                } else if (message.candidate) {
                  // Add the new ICE candidate to our connections remote description
                  pc.addIceCandidate(
                    new RTCIceCandidate(message.candidate), onSuccess, onError
                  );
                }
              });
            }

            function localDescCreated(desc) {
              pc.setLocalDescription(
                desc,
                () => sendMessage({'sdp': pc.localDescription}),
                onError
              );
            }








      

              
            
            var rect1 = { left: 160, top: 0, stroke:"<?php echo $user_one_color; ?>",strokeWidth:3,fill:'',width: 300, height: 200, id:"user_one",lockMovementX:true,lockMovementY:true};
              
            var rect2 = { left: 920, top: 0,fill: 'white', width: 300, height: 200 , id:"user_two",lockMovementX:true,lockMovementY:true,stroke:"<?php echo $user_two_color ?>",strokeWidth:3 };
            var rect3 = { left: 160, top: 410, fill: 'white', width: 300, height: 200 , id:"user_three",lockMovementX:true,lockMovementY:true,stroke:"<?php echo $user_three_color ?>",strokeWidth:3};
            var rect4 = { left: 920, top: 410, fill: 'white', width: 300, height: 200 , id:"user_four",lockMovementX:true,lockMovementY:true,stroke:"<?php echo $user_four_color; ?>",strokeWidth:3,};   

            fabric.Image.fromURL('<?php echo $doneURL; ?>', function(myImg) {
                myImg.id = "done_text";
                myImg.left = 690;
                myImg.top = 350;
                myImg.opacity = 0;
                myImg.angle = -50;
                myImg.lockMovementX = true;
                myImg.lockMovementY = true; 
                canvas.add(myImg);    
                canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
            });   

            var rect1obj = new fabric.Rect( rect1 )    
            var rect2obj = new fabric.Rect( rect2 ) 
            var rect3obj = new fabric.Rect( rect3 )
            var rect4obj = new fabric.Rect( rect4 )
            

            canvas.add(rect1obj);   
            canvas.add(rect2obj);       
            canvas.add(rect3obj);
            canvas.add(rect4obj);
            

            /***************** left top left bar **************/

            var yellow_inner_0 = new fabric.Circle({ radius: 18, fill: 'yellow',stroke:"yellow",strokeWidth:2, top: 0, left: 105,lockMovementX:true,lockMovementY:true,id:"yellow_empty_0" });
            canvas.add(yellow_inner_0);         

            canvas.add(new fabric.Circle({ radius: 18, fill: 'yellow',stroke:"yellow",strokeWidth:2, top: 50, left: 105,lockMovementX:true,lockMovementY:true,id:"yellow_empty_1" }));        

            canvas.add(new fabric.Circle({ radius: 18, fill: 'yellow',stroke:"yellow",strokeWidth:2, top: 100, left: 105,lockMovementX:true,lockMovementY:true,id:"yellow_empty_2" }));        

            canvas.add(new fabric.Circle({ radius: 18, fill: 'yellow',stroke:"yellow",strokeWidth:2, top: 150, left: 105,lockMovementX:true,lockMovementY:true,id:"yellow_empty_3" }));        

            /**************** left top bottom bar **************/
            canvas.add(new fabric.Circle({ radius: 17, fill: 'yellow',stroke:"black",strokeWidth:2, top: 210, left: 170,lockMovementX:true,lockMovementY:true,id:"empty_0" }));     

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 224,lockMovementX:true,lockMovementY:true,id:"empty_1" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 284,lockMovementX:true,lockMovementY:true,id:"empty_2" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 344,lockMovementX:true,lockMovementY:true,id:"empty_3" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 404,lockMovementX:true,lockMovementY:true,id:"empty_4" }));

            /**************** left top right bar **************/

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 0, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_10" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 42, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_9" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 84, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_8" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 126, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_7" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 168, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_6" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_5" }));

            /**************** right top right bar **************/

            canvas.add(new fabric.Circle({ radius: 18, fill: 'blue',stroke:"blue",strokeWidth:2, top: 0, left: 1240,lockMovementX:true,lockMovementY:true,id:"blue_empty_0" }));  

            canvas.add(new fabric.Circle({ radius: 18, fill: 'blue',stroke:"blue",strokeWidth:2, top: 50, left: 1240,lockMovementX:true,lockMovementY:true,id:"blue_empty_1" })); 

            canvas.add(new fabric.Circle({ radius: 18, fill: 'blue',stroke:"blue",strokeWidth:2, top: 100, left: 1240,lockMovementX:true,lockMovementY:true,id:"blue_empty_2" })); 

            canvas.add(new fabric.Circle({ radius: 18, fill: 'blue',stroke:"blue",strokeWidth:2, top: 150, left: 1240,lockMovementX:true,lockMovementY:true,id:"blue_empty_3" })); 


            /**************** right top left bar **************/

            canvas.add(new fabric.Circle({ radius: 17, fill: 'blue',stroke:"black",strokeWidth:2, top: 0, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_14" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 42, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_15" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 84, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_16" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 126, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_17" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 168, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_18" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_19" }));

            /**************** right top bottom bar **************/
            
            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 940,lockMovementX:true,lockMovementY:true,id:"empty_20" }));  

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 1000,lockMovementX:true,lockMovementY:true,id:"empty_21" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 1060,lockMovementX:true,lockMovementY:true,id:"empty_22" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 1120,lockMovementX:true,lockMovementY:true,id:"empty_23" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 1180,lockMovementX:true,lockMovementY:true,id:"empty_24" }));

            /******************** top middle bar **************/
            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 0, left: 570,lockMovementX:true,lockMovementY:true,id:"empty_11" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 0, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_12" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 0, left: 770,lockMovementX:true,lockMovementY:true,id:"empty_13" }));  

            canvas.add(new fabric.Circle({ radius: 18, fill: 'blue',stroke:"black",strokeWidth:2, top: 42, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_blue_0" }));          

            canvas.add(new fabric.Circle({ radius: 18, fill: 'blue',stroke:"black",strokeWidth:2, top: 84, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_blue_1" })); 

            canvas.add(new fabric.Circle({ radius: 18, fill: 'blue',stroke:"black",strokeWidth:2, top: 126, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_blue_2" }));           

            canvas.add(new fabric.Circle({ radius: 18, fill: 'blue',stroke:"black",strokeWidth:2, top: 168, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_blue_3" }));   

            /***************** left bottom left bar **************/
            canvas.add(new fabric.Circle({ radius: 18, fill: 'green',stroke:"green",strokeWidth:2, top: 570, left: 105,lockMovementX:true,lockMovementY:true,id:"green_empty_0" }));        

            canvas.add(new fabric.Circle({ radius: 18, fill: 'green',stroke:"green",strokeWidth:2, top: 520, left: 105,lockMovementX:true,lockMovementY:true,id:"green_empty_1" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'green',stroke:"green",strokeWidth:2, top: 470, left: 105,lockMovementX:true,lockMovementY:true,id:"green_empty_2" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'green',stroke:"green",strokeWidth:2, top: 420, left: 105,lockMovementX:true,lockMovementY:true,id:"green_empty_3" }));
            

            /**************** left bottom top bar **************/

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 170,lockMovementX:true,lockMovementY:true,id:"empty_52" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 224,lockMovementX:true,lockMovementY:true,id:"empty_51" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 284,lockMovementX:true,lockMovementY:true,id:"empty_50" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 344,lockMovementX:true,lockMovementY:true,id:"empty_49" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 404,lockMovementX:true,lockMovementY:true,id:"empty_48" }));


            /**************** left bottom right bar **************/  

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_47" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 409, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_46" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 451, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_45" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 493, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_44" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 535, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_43" }));

            canvas.add(new fabric.Circle({ radius: 17, fill: 'green',stroke:"black",strokeWidth:2, top: 577, left: 470,lockMovementX:true,lockMovementY:true,id:"empty_42" }));         


            /**************** right bottom right bar **************/

            canvas.add(new fabric.Circle({ radius: 18, fill: 'red',stroke:"red",strokeWidth:2, top: 570, left: 1240,lockMovementX:true,lockMovementY:true,id:"red_empty_0" }));        

            canvas.add(new fabric.Circle({ radius: 18, fill: 'red',stroke:"red",strokeWidth:2, top: 520, left: 1240,lockMovementX:true,lockMovementY:true,id:"red_empty_1" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'red',stroke:"red",strokeWidth:2, top: 470, left: 1240,lockMovementX:true,lockMovementY:true,id:"red_empty_2" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'red',stroke:"red",strokeWidth:2, top: 420, left: 1240,lockMovementX:true,lockMovementY:true,id:"red_empty_3" }));
            

            /**************** right bottom left bar **************/

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_33" }));  

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 409, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_34" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 451, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_35" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 493, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_36" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 533, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_37" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 575, left: 875,lockMovementX:true,lockMovementY:true,id:"empty_38" }));


            /******************** bottom middle bar **************/
            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 575, left: 570,lockMovementX:true,lockMovementY:true,id:"empty_41" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 575, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_40" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 575, left: 770,lockMovementX:true,lockMovementY:true,id:"empty_39" }));       

            canvas.add(new fabric.Circle({ radius: 18, fill: 'green',stroke:"black",strokeWidth:2, top: 521, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_green_0" }));   

            canvas.add(new fabric.Circle({ radius: 18, fill: 'green',stroke:"black",strokeWidth:2, top: 479, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_green_1" }));   

            canvas.add(new fabric.Circle({ radius: 18, fill: 'green',stroke:"black",strokeWidth:2, top: 437, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_green_2" })); 

            canvas.add(new fabric.Circle({ radius: 18, fill: 'green',stroke:"black",strokeWidth:2, top: 395, left: 670,lockMovementX:true,lockMovementY:true,id:"empty_green_3" }));   


            /**************** right bottom top bar **************/  
            
            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 940,lockMovementX:true,lockMovementY:true,id:"empty_32" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 1000,lockMovementX:true,lockMovementY:true,id:"empty_31" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 1060,lockMovementX:true,lockMovementY:true,id:"empty_30" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 367, left: 1120,lockMovementX:true,lockMovementY:true,id:"empty_29" }));

            canvas.add(new fabric.Circle({ radius: 17, fill: 'red',stroke:"black",strokeWidth:2, top: 369, left: 1180,lockMovementX:true,lockMovementY:true,id:"empty_28" }));

            /**************** middle right bar **************/  
   
            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 327, left: 1180,lockMovementX:true,lockMovementY:true,id:"empty_27" }));      

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 288, left: 1180,lockMovementX:true,lockMovementY:true,id:"empty_26" }));      

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 249, left: 1180,lockMovementX:true,lockMovementY:true,id:"empty_25" }));       


            /**************** middle left bar **************/  
   
            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 327, left: 170,lockMovementX:true,lockMovementY:true,id:"empty_53" }));      

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 288, left: 170,lockMovementX:true,lockMovementY:true,id:"empty_54" }));      

            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 249, left: 170,lockMovementX:true,lockMovementY:true,id:"empty_55" }));       

            /**************** middle middle bar **************/  

            canvas.add(new fabric.Circle({ radius: 18, fill: 'yellow',stroke:"black",strokeWidth:2, top: 293, left: 224,lockMovementX:true,lockMovementY:true,id:"empty_yellow_0" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'yellow',stroke:"black",strokeWidth:2, top: 293, left: 284,lockMovementX:true,lockMovementY:true,id:"empty_yellow_1" }));  
            canvas.add(new fabric.Circle({ radius: 18, fill: 'yellow',stroke:"black",strokeWidth:2, top: 293, left: 344,lockMovementX:true,lockMovementY:true,id:"empty_yellow_2" }));

            canvas.add(new fabric.Circle({ radius: 18, fill: 'yellow',stroke:"black",strokeWidth:2, top: 293, left: 404,lockMovementX:true,lockMovementY:true,id:"empty_yellow_3" }));


            canvas.add(new fabric.Circle({ radius: 18, fill: 'red',stroke:"black",strokeWidth:2, top: 293, left: 1120,lockMovementX:true,lockMovementY:true,id:"empty_red_0" }));     

            canvas.add(new fabric.Circle({ radius: 18, fill: 'red',stroke:"black",strokeWidth:2, top: 293, left: 1060,lockMovementX:true,lockMovementY:true,id:"empty_red_1" }));     

            canvas.add(new fabric.Circle({ radius: 18, fill: 'red',stroke:"black",strokeWidth:2, top: 293, left: 1000,lockMovementX:true,lockMovementY:true,id:"empty_red_2" }));     

            canvas.add(new fabric.Circle({ radius: 18, fill: 'red',stroke:"black",strokeWidth:2, top: 293, left: 940,lockMovementX:true,lockMovementY:true,id:"empty_red_3" }));     


            canvas.add(new fabric.Circle({ radius: 18, fill: 'white',stroke:"black",strokeWidth:2, top: 290, left: 670,lockMovementX:true,lockMovementY:true,id:"center_empty_0" }));     
            

            var objectsLength = [];
            var objs = canvas.getObjects().map(function(o) {
                //console.log(o);
                objectsLength.push({left:o.left,width:o.width,top:o.top,height:o.height,id:o.id}); 
                return o.set('active', true);
            });               

            canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = o.hasRotatingPoint = false; }); 
 
            /*fabric.Image.fromURL('https://img.icons8.com/windows/2x/macos-close.png', function(myImg) {    
                myImg.id = "bullet";
                canvas.add(myImg);  
                canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
            });*/            

            canvas.on({
                'mouse:up': function(e) {
                    if(e.target != null && e.target.id!='undefined' && e.target.id == "done_button" || e.target.id == "done_text") {
                          //console.log("sdsdsd");
                          done();
                    }              
                },
                'object:moving': function(e) {
                    //console.log(e.target.canvas);   
                },
                'object:modified': function(e) {
                    //console.log("modified");
                    //console.log(e.target.type);
                },
                'object:moved': function(e) {
                    //console.log(e.target);
                    var current_turn = $("#current_turn").val();
                    //console.log(e.target.left,e.target.top);
                    //console.log(objectsLength);

                    var movedClientX = e.target.left+22;   
                    var movedClientY = e.target.top;
                    var movedType = e.target.type;

                    //console.log(movedType);   
                    
                    var is_element_exist = 0;
                    for(var n=0; n<objectsLength.length; n++) {
                        var objectLeft =  objectsLength[n].left;
                        var objectWidth = objectsLength[n].width;
                        var objectTop = objectsLength[n].top;
                        var objectHeight = objectsLength[n].height;
                        var objectId = objectsLength[n].id;

                        //console.log(objectId);     
                        //var cacheTranslationX = objectsLength[n].cacheTranslationX;
                        //var cacheTranslationY = objectsLength[n].cacheTranslationY;

                        var totlaHeightCompare = objectHeight+objectTop;
                        var totlaWidthCompare = objectWidth+objectLeft;

                        if( ( (movedClientX>=objectLeft && movedClientX<=totlaWidthCompare) && (movedClientY>=objectTop && movedClientY<=totlaHeightCompare) &&  objectId.includes("empty_")  )  ) {  

                            //console.log("testing");

                            var centerX = (objectLeft + (objectWidth/7)) + 2;
                            var centerY = (objectTop + (objectHeight/7)) + 1;

                            
                            
                            canvas.getObjects().map(function(o) {
                              if( o.type == movedType ) {  

                                  canvas.getObjects().map(function(r) {  
                                    var existingType = r.type;
                                    var existingLeft = r.left;
                                    var existingTop = r.top;

                                    var intCenterX = parseInt(centerX);
                                    var intCenterY = parseInt(centerY);
 
                                    

                                    var diffX = Math.abs(intCenterX - existingLeft);
                                    var diffY = Math.abs(intCenterY - existingTop);

                                    if(existingType == "blue_0" || existingType == "blue_3" || existingType == "yellow_0") {
                                      console.log(existingType);     
                                      console.log("existingLeft X :"+existingLeft);
                                      console.log("existingTop Y :"+existingTop);
                                      console.log("center X :"+intCenterX);
                                      console.log("center Y :"+intCenterY);  
                                      console.log("diff X :"+diffX);
                                      console.log("diff Y :"+diffY);  
                                    }                                

                                    if((movedType == 'yellow_0' || movedType == 'yellow_1' ||  movedType == 'yellow_2' || movedType == 'yellow_3')) {  
                                              //alert(existingType);
                                          if((intCenterX == existingLeft && intCenterX == existingTop) || ( diffX<=2 && diffY<=2 )  ) {    
                                              console.log("existing type : "+existingType);

                                               if(existingType == 'blue_0') {
                                                  r.set({
                                                    left: <?php echo $blue_zero_left_position; ?>,    
                                                    top: <?php echo $blue_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_zero_left_position ?>,    
                                                    top: <?php echo $blue_zero_top_position ?>
                                                  });  
                                                  reset_marble_position('blue_0');
                                               } if(existingType == 'blue_1') {
                                                  r.set({
                                                    left: <?php echo $blue_one_left_position; ?>,    
                                                    top: <?php echo $blue_one_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_one_left_position; ?>,    
                                                    top: <?php echo $blue_one_top_position; ?>
                                                  }); 
                                                  reset_marble_position('blue_1');
                                               } if(existingType == 'blue_2') {
                                                  r.set({
                                                    left: <?php echo $blue_two_left_position; ?>,    
                                                    top: <?php echo $blue_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_two_left_position; ?>,    
                                                    top: <?php echo $blue_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('blue_2');
                                               } if(existingType == 'blue_3') {
                                                  r.set({
                                                    left: <?php echo $blue_three_left_position; ?>,    
                                                    top: <?php echo $blue_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_three_left_position; ?>,    
                                                    top: <?php echo $blue_three_top_position; ?>
                                                  }); 
                                                  reset_marble_position('blue_3');
                                               } if(existingType == 'red_0') {
                                                  r.set({
                                                    left: <?php echo $red_zero_left_position; ?>,    
                                                    top: <?php echo $red_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_zero_left_position; ?>,    
                                                    top: <?php echo $red_zero_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_0');
                                               } if(existingType == 'red_1') {
                                                  r.set({
                                                    left: <?php echo $red_one_left_position; ?>,    
                                                    top: <?php echo $red_one_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_one_left_position; ?>,    
                                                    top: <?php echo $red_one_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_1');
                                               } if(existingType == 'red_2') {
                                                  r.set({
                                                    left: <?php echo $red_two_left_position; ?>,    
                                                    top: <?php echo $red_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_two_left_position; ?>,    
                                                    top: <?php echo $red_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_2');
                                               } if(existingType == 'red_3') {
                                                  r.set({
                                                    left: <?php echo $red_three_left_position ?>,    
                                                    top: <?php echo $red_three_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_three_left_position ?>,    
                                                    top: <?php echo $red_three_top_position ?>
                                                  }); 
                                                  reset_marble_position('red_3');
                                               } if(existingType == 'green_0') {
                                                  r.set({
                                                    left: <?php echo $green_zero_left_position ?>,    
                                                    top: <?php echo $green_zero_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_zero_left_position ?>,    
                                                    top: <?php echo $green_zero_top_position ?>
                                                  }); 
                                                  reset_marble_position('green_0');
                                               } if(existingType == 'green_1') {
                                                  r.set({
                                                    left: <?php echo $green_one_left_position ?>,    
                                                    top: <?php echo $green_one_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_one_left_position ?>,    
                                                    top: <?php echo $green_one_top_position ?>
                                                  }); 
                                                  reset_marble_position('green_1');
                                               } if(existingType == 'green_2') {
                                                  r.set({
                                                    left: <?php echo $green_two_left_position ?>,    
                                                    top: <?php echo $green_two_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_two_left_position ?>,    
                                                    top: <?php echo $green_two_top_position ?>
                                                  }); 
                                                  reset_marble_position('green_2');
                                               } if(existingType == 'green_3') {
                                                  r.set({    
                                                    left: <?php echo $green_three_left_position ?>,    
                                                    top: <?php echo $green_three_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_three_left_position ?>,    
                                                    top: <?php echo $green_three_top_position ?>
                                                  }); 
                                                  reset_marble_position('green_3');
                                               }
                                            }   
                                        } else if((movedType == 'blue_0' || movedType == 'blue_1' ||  movedType == 'blue_2' || movedType == 'blue_3')) {
                                            if((intCenterX == existingLeft && intCenterX == existingTop) || ( diffX<=2 && diffY<=2 )  ) {    
                                               if(existingType == 'yellow_0') {
                                                  r.set({
                                                    left: <?php echo $yellow_zero_left_position ?>,    
                                                    top: <?php echo $yellow_zero_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_zero_left_position ?>,    
                                                    top: <?php echo $yellow_zero_top_position ?>
                                                  });  
                                                  reset_marble_position('yellow_0');
                                               } if(existingType == 'yellow_1') {
                                                  r.set({
                                                    left: <?php echo $yellow_one_left_position ?>,    
                                                    top: <?php echo $yellow_one_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_one_left_position ?>,    
                                                    top: <?php echo $yellow_one_top_position ?>
                                                  }); 
                                                  reset_marble_position('yellow_1');
                                               } if(existingType == 'yellow_2') {
                                                  r.set({
                                                    left: <?php echo $yellow_two_left_position ?>,    
                                                    top: <?php echo $yellow_two_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_two_left_position ?>,    
                                                    top: <?php echo $yellow_two_top_position ?>
                                                  }); 
                                                  reset_marble_position('yellow_2');
                                               } if(existingType == 'yellow_3') {
                                                  r.set({
                                                    left: <?php echo $yellow_three_left_position ?>,    
                                                    top: <?php echo $yellow_three_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_three_left_position ?>,    
                                                    top: <?php echo $yellow_three_top_position ?>
                                                  }); 
                                                  reset_marble_position('yellow_3');
                                               } if(existingType == 'red_0') {
                                                  r.set({
                                                    left: <?php echo $red_zero_left_position; ?>,    
                                                    top: <?php echo $red_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_zero_left_position; ?>,    
                                                    top: <?php echo $red_zero_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_0');
                                               } if(existingType == 'red_1') {
                                                  r.set({
                                                    left: <?php echo $red_one_left_position; ?>,    
                                                    top: <?php echo $red_one_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_one_left_position; ?>,    
                                                    top: <?php echo $red_one_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_1');
                                               } if(existingType == 'red_2') {
                                                  r.set({
                                                    left: <?php echo $red_two_left_position; ?>,    
                                                    top: <?php echo $red_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_two_left_position; ?>,    
                                                    top: <?php echo $red_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_2');
                                               } if(existingType == 'red_3') {
                                                  r.set({
                                                    left: <?php echo $red_three_left_position; ?>,    
                                                    top: <?php echo $red_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_three_left_position; ?>,    
                                                    top: <?php echo $red_three_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_3');
                                               } if(existingType == 'green_0') {
                                                  r.set({
                                                    left: <?php echo $green_zero_left_position; ?>,    
                                                    top: <?php echo $green_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_zero_left_position; ?>,    
                                                    top: <?php echo $green_zero_top_position; ?>
                                                  }); 
                                                  reset_marble_position('green_0');
                                               } if(existingType == 'green_1') {
                                                  r.set({
                                                    left: <?php echo $green_one_left_position; ?>,    
                                                    top: <?php echo $green_one_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_one_left_position; ?>,    
                                                    top: <?php echo $green_one_top_position; ?>
                                                  }); 
                                                  reset_marble_position('green_1');
                                               } if(existingType == 'green_2') {
                                                  r.set({
                                                    left: <?php echo $green_two_left_position; ?>,    
                                                    top: <?php echo $green_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_two_left_position; ?>,    
                                                    top: <?php echo $green_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('green_2');
                                               } if(existingType == 'green_3') {
                                                  r.set({    
                                                    left: <?php echo $green_three_left_position; ?>,    
                                                    top: <?php echo $green_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_three_left_position; ?>,    
                                                    top: <?php echo $green_three_top_position; ?>
                                                  }); 
                                                  reset_marble_position('green_3');
                                               }
                                            }   
                                        } else if((movedType == 'red_0' || movedType == 'red_1' ||  movedType == 'red_2' || movedType == 'red_3')) {
                                            if((intCenterX == existingLeft && intCenterX == existingTop) || ( diffX<=2 && diffY<=2 )  ) {    
                                               if(existingType == 'yellow_0') {
                                                  r.set({
                                                    left: <?php echo $yellow_zero_left_position; ?>,    
                                                    top: <?php echo $yellow_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_zero_left_position; ?>,    
                                                    top: <?php echo $yellow_zero_top_position; ?>
                                                  });  
                                                  reset_marble_position('yellow_0');
                                               } if(existingType == 'yellow_1') {
                                                  r.set({
                                                    left: <?php echo $yellow_one_left_position; ?>,    
                                                    top: <?php echo $yellow_one_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_one_left_position; ?>,    
                                                    top: <?php echo $yellow_one_top_position; ?>
                                                  }); 
                                                  reset_marble_position('yellow_1');
                                               } if(existingType == 'yellow_2') {
                                                  r.set({
                                                    left: <?php echo $yellow_two_left_position; ?>,    
                                                    top: <?php echo $yellow_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_two_left_position; ?>,    
                                                    top: <?php echo $yellow_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('yellow_2');
                                               } if(existingType == 'yellow_3') {
                                                  r.set({
                                                    left: <?php echo $yellow_three_left_position; ?>,    
                                                    top: <?php echo $yellow_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_three_left_position; ?>,    
                                                    top: <?php echo $yellow_three_top_position; ?>
                                                  });
                                                  reset_marble_position('yellow_3'); 
                                               } if(existingType == 'blue_0') {
                                                  r.set({
                                                    left: <?php echo $blue_zero_left_position; ?>,    
                                                    top: <?php echo $blue_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_zero_left_position; ?>,    
                                                    top: <?php echo $blue_zero_top_position; ?>
                                                  });  
                                                  reset_marble_position('blue_0'); 
                                               } if(existingType == 'blue_1') {
                                                  r.set({
                                                    left: <?php echo $blue_one_left_position ?>,    
                                                    top: <?php echo $blue_one_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_one_left_position ?>,    
                                                    top: <?php echo $blue_one_top_position ?>
                                                  }); 
                                                  reset_marble_position('blue_1'); 
                                               } if(existingType == 'blue_2') {
                                                  r.set({
                                                    left: <?php echo $blue_two_left_position; ?>,    
                                                    top: <?php echo $blue_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_two_left_position; ?>,    
                                                    top: <?php echo $blue_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('blue_2'); 
                                               } if(existingType == 'blue_3') {
                                                  r.set({
                                                    left: <?php echo $blue_three_left_position; ?>,    
                                                    top: <?php echo $blue_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_three_left_position; ?>,    
                                                    top: <?php echo $blue_three_top_position; ?>
                                                  }); 
                                                  reset_marble_position('blue_3'); 
                                               } if(existingType == 'green_0') {
                                                  r.set({
                                                    left: <?php echo $green_zero_left_position; ?>,    
                                                    top: <?php echo $green_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_zero_left_position; ?>,    
                                                    top: <?php echo $green_zero_top_position; ?>
                                                  }); 
                                                  reset_marble_position('green_0'); 
                                               } if(existingType == 'green_1') {
                                                  r.set({
                                                    left: <?php echo $green_one_left_position; ?>,    
                                                    top: <?php echo $green_one_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_one_left_position; ?>,    
                                                    top: <?php echo $green_one_top_position; ?>
                                                  });
                                                  reset_marble_position('green_1'); 
                                               } if(existingType == 'green_2') {
                                                  r.set({
                                                    left: <?php echo $green_two_left_position; ?>,    
                                                    top: <?php echo $green_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_two_left_position; ?>,    
                                                    top: <?php echo $green_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('green_2'); 
                                               } if(existingType == 'green_3') {
                                                  r.set({    
                                                    left: <?php echo $green_three_left_position; ?>,    
                                                    top: <?php echo $green_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $green_three_left_position; ?>,    
                                                    top: <?php echo $green_three_top_position; ?>
                                                  }); 
                                                  reset_marble_position('green_3'); 
                                               }
                                            }   
                                        } else if((movedType == 'green_0' || movedType == 'green_1' ||  movedType == 'green_2' || movedType == 'green_3')) {
                                            if((intCenterX == existingLeft && intCenterX == existingTop) || ( diffX<=2 && diffY<=2 )  ) {    
                                               if(existingType == 'yellow_0') {
                                                  r.set({
                                                    left: <?php echo $yellow_zero_left_position; ?>,    
                                                    top: <?php echo $yellow_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_zero_left_position; ?>,    
                                                    top: <?php echo $yellow_zero_top_position; ?>
                                                  });  
                                                  reset_marble_position('yellow_0'); 
                                               } if(existingType == 'yellow_1') {
                                                  r.set({
                                                    left: <?php echo $yellow_one_left_position; ?>,    
                                                    top: <?php echo $yellow_one_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_one_left_position; ?>,    
                                                    top: <?php echo $yellow_one_top_position; ?>
                                                  }); 
                                                  reset_marble_position('yellow_1'); 
                                               } if(existingType == 'yellow_2') {
                                                  r.set({
                                                    left: <?php echo $yellow_two_left_position; ?>,    
                                                    top: <?php echo $yellow_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_two_left_position; ?>,    
                                                    top: <?php echo $yellow_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('yellow_2'); 
                                               } if(existingType == 'yellow_3') {
                                                  r.set({
                                                    left: <?php echo $yellow_three_left_position; ?>,    
                                                    top: <?php echo $yellow_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $yellow_three_left_position; ?>,    
                                                    top: <?php echo $yellow_three_top_position; ?>
                                                  }); 
                                                  reset_marble_position('yellow_3'); 
                                               } if(existingType == 'blue_0') {
                                                  r.set({
                                                    left: <?php echo $blue_zero_left_position; ?>,    
                                                    top: <?php echo $blue_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_zero_left_position; ?>,    
                                                    top: <?php echo $blue_zero_top_position; ?>
                                                  });  
                                                  reset_marble_position('blue_0'); 
                                               } if(existingType == 'blue_1') {
                                                  r.set({
                                                    left: <?php echo $blue_one_left_position ?>,    
                                                    top: <?php echo $blue_one_top_position ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_one_left_position ?>,    
                                                    top: <?php echo $blue_one_top_position ?>
                                                  }); 
                                                  reset_marble_position('blue_1'); 
                                               } if(existingType == 'blue_2') {
                                                  r.set({
                                                    left: <?php echo $blue_two_left_position; ?>,    
                                                    top: <?php echo $blue_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_two_left_position; ?>,    
                                                    top: <?php echo $blue_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('blue_2'); 
                                               } if(existingType == 'blue_3') {
                                                  r.set({
                                                    left: <?php echo $blue_three_left_position; ?>,    
                                                    top: <?php echo $blue_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $blue_three_left_position; ?>,    
                                                    top: <?php echo $blue_three_top_position; ?>
                                                  }); 
                                                  reset_marble_position('blue_3'); 
                                               } if(existingType == 'red_0') {
                                                  r.set({
                                                    left: <?php echo $red_zero_left_position; ?>,    
                                                    top: <?php echo $red_zero_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_zero_left_position; ?>,    
                                                    top: <?php echo $red_zero_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_0'); 
                                               } if(existingType == 'red_1') {
                                                  r.set({
                                                    left: <?php echo $red_one_left_position; ?>,    
                                                    top: <?php echo $red_one_top_position; ?>
                                                  });    
                                                  r.setCoords({
                                                    left: <?php echo $red_one_left_position; ?>,    
                                                    top: <?php echo $red_one_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_1'); 
                                               } if(existingType == 'red_2') {
                                                  r.set({
                                                    left: <?php echo $red_two_left_position; ?>,    
                                                    top: <?php echo $red_two_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_two_left_position; ?>,    
                                                    top: <?php echo $red_two_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_2'); 
                                               } if(existingType == 'red_3') {
                                                  r.set({
                                                    left: <?php echo $red_three_left_position; ?>,    
                                                    top: <?php echo $red_three_top_position; ?>
                                                  });
                                                  r.setCoords({
                                                    left: <?php echo $red_three_left_position; ?>,    
                                                    top: <?php echo $red_three_top_position; ?>
                                                  }); 
                                                  reset_marble_position('red_3'); 
                                               }   
                                            }         
                                        }
                                  });  
                                  

                                  o.set({    
                                    left: centerX,
                                    top: centerY 
                                  });
                                  o.setCoords({
                                    left: centerX,
                                    top: centerY
                                  });
                              }              
                            });       
                            is_element_exist = 1;        
                            update_marble_position(movedType,objectId);
                        }   
                        //console.log(is_element_exist);   
                    }  

                    if(is_element_exist == 0) {
                        canvas.getObjects().map(function(o) {
                            if( o.type == movedType && movedType == 'yellow_3'  ) { 
                                o.set({
                                  left: <?php echo $yellow_three_left_position; ?>,    
                                  top: <?php echo $yellow_three_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $yellow_three_left_position; ?>,    
                                  top: <?php echo $yellow_three_top_position; ?>
                                });
                            } else if( o.type == movedType && movedType == 'yellow_2'  ) { 
                                o.set({
                                  left: <?php echo $yellow_two_left_position; ?>,    
                                  top: <?php echo $yellow_two_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $yellow_two_left_position; ?>,    
                                  top: <?php echo $yellow_two_top_position; ?>
                                });
                            } else if( o.type == movedType && movedType == 'yellow_1'  ) { 
                                o.set({
                                  left: <?php echo $yellow_one_left_position; ?>,    
                                  top: <?php echo $yellow_one_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $yellow_one_left_position; ?>,    
                                  top: <?php echo $yellow_one_top_position; ?>
                                });
                            } else if( o.type == movedType && movedType == 'yellow_0'  ) { 
                                o.set({
                                  left: <?php echo $yellow_zero_left_position; ?>,    
                                  top: <?php echo $yellow_zero_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $yellow_zero_left_position; ?>,    
                                  top: <?php echo $yellow_zero_top_position; ?>
                                });
                            } else if( o.type == movedType && movedType == 'blue_3'  ) { 
                                o.set({
                                  left: <?php echo $blue_three_left_position; ?>,    
                                  top: <?php echo $blue_three_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $blue_three_left_position; ?>,    
                                  top: <?php echo $blue_three_top_position; ?>
                                });  
                            } else if( o.type == movedType && movedType == 'blue_2'  ) { 
                                o.set({
                                  left: <?php echo $blue_two_left_position; ?>,    
                                  top: <?php echo $blue_two_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $blue_two_left_position; ?>,    
                                  top: <?php echo $blue_two_top_position; ?>
                                });  
                            } else if( o.type == movedType && movedType == 'blue_1'  ) { 
                                o.set({
                                  left: <?php echo $blue_one_left_position; ?>,    
                                  top: <?php echo $blue_one_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $blue_one_left_position; ?>,    
                                  top: <?php echo $blue_one_top_position; ?>
                                });  
                            } else if( o.type == movedType && movedType == 'blue_0'  ) { 
                                o.set({
                                  left: <?php echo $blue_zero_left_position; ?>,    
                                  top: <?php echo $blue_zero_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $blue_zero_left_position; ?>,    
                                  top: <?php echo $blue_zero_top_position; ?>
                                });  
                            } else if( o.type == movedType && movedType == 'red_3'  ) { 
                                o.set({
                                  left: <?php echo $red_three_left_position; ?>,    
                                  top: <?php echo $red_three_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $red_three_left_position; ?>,    
                                  top: <?php echo $red_three_top_position; ?>
                                });  
                            } else if( o.type == movedType && movedType == 'red_2'  ) { 
                                o.set({
                                  left: <?php echo $red_two_left_position; ?>,    
                                  top: <?php echo $red_two_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $red_two_left_position; ?>,    
                                  top: <?php echo $red_two_top_position; ?>
                                });  
                            } else if( o.type == movedType && movedType == 'red_1'  ) { 
                                o.set({
                                  left: <?php echo $red_one_left_position; ?>,    
                                  top: <?php echo $red_one_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $red_one_left_position; ?>,    
                                  top: <?php echo $red_one_top_position; ?>
                                });  
                            } else if( o.type == movedType && movedType == 'red_0'  ) { 
                                o.set({
                                  left: <?php echo $red_zero_left_position; ?>,    
                                  top: <?php echo $red_zero_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $red_zero_left_position; ?>,    
                                  top: <?php echo $red_zero_top_position; ?>
                                });  
                            } else if( o.type == movedType && movedType == 'green_3'  ) { 
                                o.set({
                                  left: <?php echo $green_three_left_position; ?>,    
                                  top: <?php echo $green_three_top_position; ?> 
                                });
                                o.setCoords({
                                  left: <?php echo $green_three_left_position; ?>,    
                                  top: <?php echo $green_three_top_position; ?> 
                                });      
                            } else if( o.type == movedType && movedType == 'green_2'  ) { 
                                o.set({
                                  left: <?php echo $green_two_left_position; ?>,    
                                  top: <?php echo $green_two_top_position; ?> 
                                });
                                o.setCoords({
                                  left: <?php echo $green_two_left_position; ?>,    
                                  top: <?php echo $green_two_top_position; ?> 
                                });  
                            } else if( o.type == movedType && movedType == 'green_1'  ) { 
                                o.set({
                                  left: <?php echo $green_one_left_position; ?>,    
                                  top: <?php echo $green_one_top_position; ?>  
                                });
                                o.setCoords({
                                  left: <?php echo $green_one_left_position; ?>,    
                                  top: <?php echo $green_one_top_position; ?>  
                                });  
                            } else if( o.type == movedType && movedType == 'green_0'  ) { 
                                o.set({
                                  left: <?php echo $green_zero_left_position; ?>,    
                                  top: <?php echo $green_zero_top_position; ?>  
                                });
                                o.setCoords({    
                                  left: <?php echo $green_zero_left_position; ?>,    
                                  top: <?php echo $green_zero_top_position; ?>  
                                });      
                            }            
                        });
                    }
                    

                },
             });

             

            
            /************************************/  
 
            //canvas.renderAll();
            //window.addEventListener('resize', resizeCanvas, false);
            function resizeCanvas() {
                  var innerWidth = window.innerWidth - 50;
                  canvas.setHeight(window.innerHeight);
                  canvas.setWidth(innerWidth);
                  var originalWidth = 1300;
                  var originalHeight = 980;
                  canvas.setZoom(innerWidth/originalWidth);
                  canvas.renderAll(); 
            }     



            fabric.util.requestAnimFrame(function render() {

                    var innerWidth = window.innerWidth-2; // - 50   

                    canvas.setHeight(window.innerHeight);
                    canvas.setWidth(innerWidth); 

                    var originalWidth = 1370; //1300
                    var originalHeight = 980; //980    

                    canvas.setZoom(innerWidth/originalWidth);
                    canvas.renderAll();
                    fabric.util.requestAnimFrame(render);
            });  
                
    })();  

    function deleteSelectedObjectsFromCanvas(canvas,selected_id){
        var selection = canvas.getObjects();
        Object.keys(selection).forEach(function(key) {
            var element = selection[key];
            if(element.id == selected_id) {
              canvas.remove(element);  
            }
        });
        canvas.discardActiveObject();
        canvas.requestRenderAll();
    }    

    var checkId = '';

    function task(canvas,n,turn_bullet) {
          setTimeout(function () {
                //console.log(n);
                checkId = "empty_"+n; 
                var objectLeft = "";
                var objectTop = "";
                canvas.getObjects().map(function(o) {
                    if(o.id == checkId) {
                      objectLeft = o.left;
                      objectTop = o.top;
                    }
                    if(o.type == turn_bullet) {
                        o.set({
                          left: objectLeft,
                          top: objectTop
                        });
                        o.setCoords({
                          left: objectLeft,
                          top: objectTop
                        });
                    }  
                });  
          }, 1000*n);
    }

    function moveToEmptyCircle(canvas,numbersToMove) {
        var current_turn = $("#current_turn").val();
        //deleteSelectedObjectsFromCanvas(canvas,'remoteCanvasVideo2');
        var turn_bullet = "";
        if(current_turn == "1") {
            turn_bullet = "yellow_4";
        }

        for(var n =1; n<=numbersToMove; n++) {
            task(canvas,n,turn_bullet);   
        }
        
    } 
 
    </script>   

    <script type="text/javascript">

    function reset_marble_position(marble_id) {
        $.ajax({     
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_VideoChat_Controller::reset_marble_position","user_id":"<?php echo $user_id; ?>","room_id":"<?php echo $room_id; ?>","marble_id":marble_id},
            success: function (data) {       
                var result =  JSON.parse(data);
                if (result.status == 1) {  
                    
                }
            }
        });
    }  

    function update_marble_position(movedType,objectId) {
        $("#current_turn").val(''); 
        $.ajax({     
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_VideoChat_Controller::update_marble_position","user_id":"<?php echo $user_id; ?>","objectID":objectId,"movedType":movedType,"room_id":"<?php echo $room_id; ?>"},
            success: function (data) {       
                var result =  JSON.parse(data);
                if (result.status == 1) {  
                    var next_turn = result.next_turn;
                    $("#current_turn").val(next_turn);
                }
            }
        });
    }  

    function addUser(stream_id,stream,typeofvideo,dron_id) {      
        $.ajax({    
            type: 'POST',    
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_VideoChat_Controller::add_stream_user","user_id":"<?php echo $user_id; ?>","stream_id":stream_id,"room_id":"<?php echo $room_id; ?>","type":typeofvideo,"drone_id":dron_id},
            success: function (data) {   
                var result =  JSON.parse(data);
                if (result.status == 1) {   
                    if(result.user_position == '0') {     

                        if(stream != '') {
                          video1El.srcObject = stream;
                          canvas.add(video1);  
                          video1.moveTo(0); 
                          video1.getElement().play();  

                          fabric.Image.fromURL('<?php echo $flatYelURL; ?>', function(myImg) {
                                    myImg.type = "yellow_0";
                                    myImg.id = "";
                                    myImg.left = <?php echo $yellow_zero_left_position; ?>;
                                    myImg.top = <?php echo $yellow_zero_top_position; ?>;
                                    myImg.lockMovementX = true;
                                    myImg.lockMovementY = true;
                                    canvas.add(myImg); 
                                    canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                          });    
                          
                          
                          fabric.Image.fromURL('<?php echo $flatYelURL; ?>', function(myImg) {
                                    myImg.type = "yellow_1";
                                    myImg.id = "";
                                    myImg.left = <?php echo $yellow_one_left_position; ?>;
                                    myImg.top = <?php echo $yellow_one_top_position; ?>;
                                    myImg.lockMovementX = true;
                                    myImg.lockMovementY = true;
                                    canvas.add(myImg); 
                                    canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                                });
                          
                          
                          fabric.Image.fromURL('<?php echo $flatYelURL; ?>', function(myImg) {
                                    myImg.type = "yellow_2";
                                    myImg.id = "";
                                    myImg.left = <?php echo $yellow_two_left_position; ?>;
                                    myImg.top = <?php echo $yellow_two_top_position; ?>;
                                    myImg.lockMovementX = true;
                                    myImg.lockMovementY = true;
                                    canvas.add(myImg); 
                                    canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                                });
                          
                          
                          
                          fabric.Image.fromURL('<?php echo $flatYelURL; ?>', function(myImg) {
                                    myImg.type = "yellow_3";
                                    myImg.id = "";
                                    myImg.left = <?php echo $yellow_three_left_position; ?>;
                                    myImg.top = <?php echo $yellow_three_top_position; ?>;
                                    myImg.lockMovementX = true;
                                    myImg.lockMovementY = true;
                                    canvas.add(myImg); 
                                    canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                                });

                        } else {
                          fabric.Image.fromURL('<?php echo $noImageURL; ?>', function(myImg) {
                            myImg.id = "no_image";
                            myImg.left = 185;
                            myImg.top = 2;
                            myImg.width = 250;
                            myImg.height = 200;    
                            myImg.lockMovementX = true;
                            myImg.lockMovementY = true; 
                            canvas.add(myImg);    
                            canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                          });
                        }

                    } else if(result.user_position == '1') { 

                        if(stream != '') {
                          $("#remote1").val(stream_id);
                          remoteVideo1El.srcObject = stream;
                          canvas.add(remoteVideo1);    
                          remoteVideo1.moveTo(0); 
                          remoteVideo1.getElement().play(); 

                           fabric.Image.fromURL('<?php echo $flatBlueURL ?>', function(myImg) {
                                    myImg.type = "blue_0";
                                    myImg.left = <?php echo $blue_zero_left_position; ?>;
                                    myImg.top = <?php echo $blue_zero_top_position; ?>;
                                    myImg.lockMovementX = true;
                                    myImg.lockMovementY = true;
                                    canvas.add(myImg); 
                                    canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                                });     
                          
                          
                          fabric.Image.fromURL('<?php echo $flatBlueURL ?>', function(myImg) {
                                    myImg.type = "blue_1";
                                    myImg.left = <?php echo $blue_one_left_position; ?>;
                                    myImg.top = <?php echo $blue_one_top_position; ?>;
                                    myImg.lockMovementX = true;
                                    myImg.lockMovementY = true;
                                    canvas.add(myImg); 
                                    canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                                });
                          
                          fabric.Image.fromURL('<?php echo $flatBlueURL ?>', function(myImg) {
                                    myImg.type = "blue_2";
                                    myImg.left = <?php echo $blue_two_left_position; ?>;
                                    myImg.top = <?php echo $blue_two_top_position; ?>;
                                    myImg.lockMovementX = true;
                                    myImg.lockMovementY = true;
                                    canvas.add(myImg); 
                                    canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                                });
                          
                          
                          
                          fabric.Image.fromURL('<?php echo $flatBlueURL ?>', function(myImg) {
                                    myImg.type = "blue_3";
                                    myImg.left = <?php echo $blue_three_left_position; ?>;
                                    myImg.top = <?php echo $blue_three_top_position; ?>;
                                    myImg.lockMovementX = true;
                                    myImg.lockMovementY = true;
                                    canvas.add(myImg); 
                                    canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                                });
                          
      


                        } else {
                          fabric.Image.fromURL('<?php echo $noImageURL; ?>', function(myImg) {
                            myImg.id = "no_image";
                            myImg.left = 945;
                            myImg.top = 2;
                            myImg.width = 250;
                            myImg.height = 200;    
                            myImg.lockMovementX = true;
                            myImg.lockMovementY = true; 
                            canvas.add(myImg);    
                            canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                          });
                        }
                        
                    } else if(result.user_position == '2') {    

                        if(stream != '') {
                            $("#remote3").val(stream_id);
                            remoteVideo3El.srcObject = stream;
                            canvas.add(remoteVideo3);    
                            remoteVideo3.moveTo(0); 
                            remoteVideo3.getElement().play(); 

                            fabric.Image.fromURL('<?php echo $flatRedURL ?>', function(myImg) {
                                myImg.type = "red_0";
                                myImg.id = "";
                                myImg.left = <?php echo $red_zero_left_position; ?>;
                                myImg.top = <?php echo $red_zero_top_position; ?>;
                                myImg.lockMovementX = true;
                                myImg.lockMovementY = true;
                                canvas.add(myImg); 
                                canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                            });
                      
                      
                            fabric.Image.fromURL('<?php echo $flatRedURL ?>', function(myImg) {
                                myImg.type = "red_1";
                                myImg.id = "";
                                myImg.left = <?php echo $red_one_left_position; ?>;
                                myImg.top = <?php echo $red_one_top_position; ?>;
                                myImg.lockMovementX = true;
                                myImg.lockMovementY = true;
                                canvas.add(myImg); 
                                canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                            });
                      
                            fabric.Image.fromURL('<?php echo $flatRedURL ?>', function(myImg) {
                                myImg.type = "red_2";
                                myImg.id = "";
                                myImg.left = <?php echo $red_two_left_position; ?>;
                                myImg.top = <?php echo $red_two_top_position; ?>;
                                myImg.lockMovementX = true;
                                myImg.lockMovementY = true;
                                canvas.add(myImg); 
                                canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                            });
                      
                            fabric.Image.fromURL('<?php echo $flatRedURL ?>', function(myImg) {
                                myImg.type = "red_3";
                                myImg.id = "";
                                myImg.left = <?php echo $red_three_left_position; ?>;
                                myImg.top = <?php echo $red_three_top_position; ?>;
                                myImg.lockMovementX = true;
                                myImg.lockMovementY = true;
                                canvas.add(myImg); 
                                canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                            });

                            
                        } else {    
                            fabric.Image.fromURL('<?php echo $noImageURL; ?>', function(myImg) {
                            myImg.id = "no_image";
                            myImg.left = 945;
                            myImg.top = 392;       
                            myImg.width = 250;
                            myImg.height = 200;    
                            myImg.lockMovementX = true;
                            myImg.lockMovementY = true; 
                            canvas.add(myImg);    
                            canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                            });  
                        }  
                        
                    } else if(result.user_position == '3') {

                       if(stream != '') {
                          $("#remote2").val(stream_id);
                          remoteVideo2El.srcObject = stream;
                          canvas.add(remoteVideo2);    
                          remoteVideo2.moveTo(0);     
                          remoteVideo2.getElement().play();     

                          fabric.Image.fromURL('<?php echo $flatGrnURL ?>', function(myImg) {
                                myImg.type = "green_0";
                                myImg.id = '';
                                myImg.left = <?php echo $green_zero_left_position; ?>;
                                myImg.top = <?php echo $green_zero_top_position; ?>;
                                myImg.lockMovementX = true;
                                myImg.lockMovementY = true;
                                canvas.add(myImg); 
                                canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                          });
                      
                      
                          fabric.Image.fromURL('<?php echo $flatGrnURL ?>', function(myImg) {
                                      myImg.type = "green_1";
                                      myImg.id = '';
                                      myImg.left = <?php echo $green_one_left_position; ?>;
                                      myImg.top = <?php echo $green_one_top_position; ?>;
                                      myImg.lockMovementX = true;
                                      myImg.lockMovementY = true;
                                      canvas.add(myImg); 
                                      canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                          });
                            
                          fabric.Image.fromURL('<?php echo $flatGrnURL ?>', function(myImg) {
                                      myImg.type = "green_2";
                                      myImg.id = '';
                                      myImg.left = <?php echo $green_two_left_position; ?>;
                                      myImg.top = <?php echo $green_two_top_position; ?>;
                                      myImg.lockMovementX = true;
                                      myImg.lockMovementY = true;
                                      canvas.add(myImg); 
                                      canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                          });  
                            
                          fabric.Image.fromURL('<?php echo $flatGrnURL ?>', function(myImg) {
                                      myImg.type = "green_3";
                                      myImg.id = "";
                                      myImg.left = <?php echo $green_three_left_position; ?>;
                                      myImg.top = <?php echo $green_three_top_position; ?>;
                                      myImg.lockMovementX = true;
                                      myImg.lockMovementY = true;
                                      canvas.add(myImg); 
                                      canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                          });

                        } else {
                          fabric.Image.fromURL('<?php echo $noImageURL; ?>', function(myImg) {
                            myImg.id = "no_image";
                            myImg.left = 185;
                            myImg.top = 392;
                            myImg.width = 250;
                            myImg.height = 200;    
                            myImg.lockMovementX = true;
                            myImg.lockMovementY = true; 
                            canvas.add(myImg);    
                            canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
                          });
                        }
                        
                    }
                    setInterval(function(){ get_online_user() }, 3000);  
                }
            }
        });          
    }

    function done() {
        canvas.getObjects().map(function(o) {
            if(o.id == 'done_text') {
              o.set({
                  opacity: 0
              });
            }  
        });
        var current_turn = $("#turn_user_id").val();  
        var dice_result = $("#dice_result").val();
        if(current_turn != '<?php echo $user_id; ?>') {
          toastr.error('You need to wait for your turn');
          return false;
        }
        $.ajax({    
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_VideoChat_Controller::done","user_id":"<?php echo $user_id; ?>","room_id":"<?php echo $room_id; ?>","dice_result":dice_result},
            success: function (data) {   
                var result =  JSON.parse(data);
                if (result.status == 1) {  
                    var next_turn = result.next_turn;
                    $("#current_turn").val(next_turn);
                    toastr.success('Your turn is completed,please wait for the next turn');
                }
            }
        }); 
    }

    function userColor(id,type) {
      var id_selector = "#"+id+'_'+type;
      var attr = $(id_selector).attr('name');
      if (typeof attr !== 'undefined' && attr !== false) {
        $(id_selector).css('background-color','');  
        $(id_selector).removeAttr('name');
      } else {    
        $(id_selector).css('background-color',type);  
        $(id_selector).attr('name','');
      }
    }

    function saveUserColor() {
        $("#mymodal").modal('hide');
        Swal.fire({
          icon: 'info',
          title: '<strong>Are you sure want to assign color access and lock the game ?</strong>',
          showConfirmButton : true,    
          showCloseButton: true,   
          showCancelButton: true,
          preConfirm: (login) => {
            var user_one_yellow = '';
            var user_one_blue = '';
            var user_one_red = '';
            var user_one_green = '';

            var user_two_yellow = '';
            var user_two_blue = '';
            var user_two_red = '';
            var user_two_green = '';

            var user_three_yellow = '';
            var user_three_blue = '';
            var user_three_red = '';
            var user_three_green = '';

            var user_four_yellow = '';
            var user_four_blue = '';
            var user_four_red = '';
            var user_four_green = '';

            if (typeof $("#one_yellow").attr('name') !== 'undefined' && $("#one_yellow").attr('name') !== false) {
              user_one_yellow  = 1;
            }
            if (typeof $("#one_blue").attr('name') !== 'undefined' && $("#one_blue").attr('name') !== false) {
              user_one_blue  = 1;
            }
            if (typeof $("#one_red").attr('name') !== 'undefined' && $("#one_red").attr('name') !== false) {
              user_one_red  = 1;
            }
            if (typeof $("#one_green").attr('name') !== 'undefined' && $("#one_green").attr('name') !== false) {
              user_one_green  = 1;
            }

            if (typeof $("#two_yellow").attr('name') !== 'undefined' && $("#two_yellow").attr('name') !== false) {
              user_two_yellow  = 1;
            }
            if (typeof $("#two_blue").attr('name') !== 'undefined' && $("#two_blue").attr('name') !== false) {
              user_two_blue  = 1;
            }
            if (typeof $("#two_red").attr('name') !== 'undefined' && $("#two_red").attr('name') !== false) {
              user_two_red  = 1;
            }
            if (typeof $("#two_green").attr('name') !== 'undefined' && $("#two_green").attr('name') !== false) {
              user_two_green  = 1;
            }

            if (typeof $("#three_yellow").attr('name') !== 'undefined' && $("#three_yellow").attr('name') !== false) {
              user_three_yellow  = 1;
            }
            if (typeof $("#three_blue").attr('name') !== 'undefined' && $("#three_blue").attr('name') !== false) {
              user_three_blue  = 1;
            }
            if (typeof $("#three_red").attr('name') !== 'undefined' && $("#three_red").attr('name') !== false) {
              user_three_red  = 1;
            }
            if (typeof $("#three_green").attr('name') !== 'undefined' && $("#three_green").attr('name') !== false) {
              user_three_green  = 1;
            }

            if (typeof $("#four_yellow").attr('name') !== 'undefined' && $("#four_yellow").attr('name') !== false) {
              user_four_yellow  = 1;
            }
            if (typeof $("#four_blue").attr('name') !== 'undefined' && $("#four_blue").attr('name') !== false) {
              user_four_blue  = 1;
            }
            if (typeof $("#four_red").attr('name') !== 'undefined' && $("#four_red").attr('name') !== false) {
              user_four_red  = 1;
            }
            if (typeof $("#four_green").attr('name') !== 'undefined' && $("#four_green").attr('name') !== false) {
              user_four_green  = 1;
            }

            
            $.ajax({    
                type: 'POST',      
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {"action": "WCP_VideoChat_Controller::save_color","user_id":"<?php echo $user_id; ?>","room_id":"<?php echo $room_id; ?>","user_one_yellow":user_one_yellow,"user_one_blue":user_one_blue,"user_one_red":user_one_red,"user_one_green":user_one_green,"user_two_yellow":user_two_yellow,"user_two_blue":user_two_blue,"user_two_red":user_two_red,"user_two_green":user_two_green,"user_three_yellow":user_three_yellow,"user_three_blue":user_three_blue,"user_three_red":user_three_red,"user_three_green":user_three_green,"user_four_yellow":user_four_yellow,"user_four_blue":user_four_blue,"user_four_red":user_four_red,"user_four_green":user_four_green},      
                success: function (data) {
                    var result =  JSON.parse(data);
                    if(result.status == 1) {  
                        toastr.success('Color applied successfully');
                        $("#mymodal").modal('hide');
                    }
                }
            });
          }
        })


        
    }

    function setColor() {
        $("#mymodal").modal('show');
    }

    function resetToOriginalPosition() {
      canvas.getObjects().map(function(o) {
                            if( o.type == 'yellow_3'  ) { 
                                o.set({
                                  left : <?php echo $yellow_three_left_position; ?>,
                                  top : <?php echo $yellow_three_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $yellow_three_left_position; ?>,
                                  top : <?php echo $yellow_three_top_position; ?>
                                });
                            } else if( o.type == 'yellow_2'  ) { 
                                o.set({
                                  left : <?php echo $yellow_two_left_position; ?>,
                                  top : <?php echo $yellow_two_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $yellow_two_left_position; ?>,
                                  top : <?php echo $yellow_two_top_position; ?>
                                });
                            } else if( o.type == 'yellow_1'  ) { 
                                o.set({
                                  left : <?php echo $yellow_one_left_position; ?>,
                                  top : <?php echo $yellow_one_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $yellow_one_left_position; ?>,
                                  top : <?php echo $yellow_one_top_position; ?>
                                });
                            } else if( o.type == 'yellow_0'  ) { 
                                o.set({
                                  left : <?php echo $yellow_zero_left_position; ?>,
                                  top : <?php echo $yellow_zero_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $yellow_zero_left_position; ?>,
                                  top : <?php echo $yellow_zero_top_position; ?>
                                });
                            } else if( o.type == 'blue_3'  ) { 
                                o.set({
                                  left : <?php echo $blue_three_left_position; ?>,
                                  top : <?php echo $blue_three_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $blue_three_left_position; ?>,
                                  top : <?php echo $blue_three_top_position; ?>
                                });  
                            } else if( o.type == 'blue_2'  ) { 
                                o.set({
                                  left : <?php echo $blue_two_left_position; ?>,
                                  top : <?php echo $blue_two_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $blue_two_left_position; ?>,
                                  top : <?php echo $blue_two_top_position; ?>
                                });  
                            } else if( o.type == 'blue_1'  ) { 
                                o.set({
                                  left : <?php echo $blue_one_left_position; ?>,
                                  top : <?php echo $blue_one_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $blue_one_left_position; ?>,
                                  top : <?php echo $blue_one_top_position; ?>
                                });  
                            } else if( o.type == 'blue_0'  ) { 
                                o.set({
                                  left : <?php echo $blue_zero_left_position; ?>,
                                  top : <?php echo $blue_zero_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $blue_zero_left_position; ?>,
                                  top : <?php echo $blue_zero_top_position; ?>
                                });  
                            } else if( o.type == 'red_3'  ) { 
                                o.set({
                                  left : <?php echo $red_three_left_position; ?>,
                                  top : <?php echo $red_three_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $red_three_left_position; ?>,
                                  top : <?php echo $red_three_top_position; ?>
                                });  
                            } else if( o.type == 'red_2'  ) { 
                                o.set({
                                  left : <?php echo $red_two_left_position; ?>,
                                  top : <?php echo $red_two_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $red_two_left_position; ?>,
                                  top : <?php echo $red_two_top_position; ?>
                                });  
                            } else if( o.type == 'red_1'  ) { 
                                o.set({
                                  left : <?php echo $red_one_left_position; ?>,
                                  top : <?php echo $red_one_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $red_one_left_position; ?>,
                                  top : <?php echo $red_one_top_position; ?>
                                });  
                            } else if( o.type == 'red_0'  ) { 
                                o.set({
                                  left : <?php echo $red_zero_left_position; ?>,
                                  top : <?php echo $red_zero_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $red_zero_left_position; ?>,
                                  top : <?php echo $red_zero_top_position; ?>
                                });  
                            } else if( o.type == 'green_3'  ) { 
                                o.set({
                                  left : <?php echo $green_three_left_position; ?>,
                                  top : <?php echo $green_three_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $green_three_left_position; ?>,
                                  top : <?php echo $green_three_top_position; ?>
                                });      
                            } else if( o.type == 'green_2'  ) { 
                                o.set({
                                  left : <?php echo $green_two_left_position; ?>,
                                  top : <?php echo $green_two_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $green_two_left_position; ?>,
                                  top : <?php echo $green_two_top_position; ?>
                                });  
                            } else if( o.type == 'green_1'  ) { 
                                o.set({
                                  left : <?php echo $green_one_left_position; ?>,
                                  top : <?php echo $green_one_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $green_one_left_position; ?>,
                                  top : <?php echo $green_one_top_position; ?>
                                });  
                            } else if( o.type == 'green_0'  ) { 
                                o.set({
                                  left : <?php echo $green_zero_left_position; ?>,
                                  top : <?php echo $green_zero_top_position; ?>
                                });
                                o.setCoords({
                                  left : <?php echo $green_zero_left_position; ?>,
                                  top : <?php echo $green_zero_top_position; ?>
                                });  
                            }            
                        });
    }

    function resetMarble() {
        $.ajax({    
            type: 'POST',    
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_VideoChat_Controller::reset_game","user_id":"<?php echo $user_id; ?>","room_id":"<?php echo $room_id; ?>"},
            success: function (data) {
                var result =  JSON.parse(data);  
                if(result.status == 1) {  
                    resetToOriginalPosition();
                    toastr.success('Game reset successfully');
                    $("#reset_game").modal('hide');
                }
            }
        });    
    }

    function resetGame() {
        <?php if($is_admin == "1") { ?> 
          $("#reset_game").modal('show');
        <?php } ?>      
    }

    function get_online_user() {
        $.ajax({    
            type: 'POST',    
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_VideoChat_Controller::get_online_user","user_id":"<?php echo $user_id; ?>","room_id":"<?php echo $room_id; ?>"},
            success: function (data) {
                var result =  JSON.parse(data);
                var loadUserWithoutVideo = result.loadUserWithoutVideo;

                var color_1 = result.color_1;
                var color_2 = result.color_2;
                var color_3 = result.color_3;
                var color_4 = result.color_4;

                if(color_1 == "" || color_1 == null) {
                  color_1 = '<?php echo $user_one_color; ?>';
                } 
                canvas.getObjects().map(function(o) {
                    if(o.id == 'user_one') {
                      o.set({
                          stroke: color_1
                      });
                    }  
                });
                if(color_2 == ""  || color_2 == null) {
                  color_2 = '<?php echo $user_two_color; ?>';
                }
                canvas.getObjects().map(function(o) {
                    if(o.id == 'user_two') {
                      o.set({
                          stroke: color_2
                      });
                    }  
                });
                if(color_3 == "" ||  color_3 == null) {
                  color_3 = '<?php echo $user_three_color; ?>';
                }
                canvas.getObjects().map(function(o) {
                    if(o.id == 'user_three') {
                      o.set({
                          stroke: color_3
                      });
                    }  
                });
                if(color_4 == ""  || color_4 == null) {
                  color_4 = '<?php echo $user_four_color; ?>';
                } 
                canvas.getObjects().map(function(o) {
                    if(o.id == 'user_four') {
                      o.set({
                          stroke: color_4
                      });
                    }  
                });


                if (result.status == 1) {  
                    var stream_data = result.stream_data;
                    var user_stream = result.user_stream;
                    var marble_data = result.current_marble_id;
                    var next_turn = result.current_turn;
                    var turn_user_id = result.turn_user_id;

                    $("#current_turn").val(next_turn);  
                    $("#turn_user_id").val(turn_user_id);  

                    if(turn_user_id == '<?php echo $user_id ?>') {
                        /*if(is_locked_dice == "") {
                          is_your_turn = 1;  
                        }*/
                        
                        $("#dice").show();
                        canvas.getObjects().map(function(o) {
                            if(o.id == 'done_text') {
                              o.set({
                                  opacity: 1    
                              });
                            }  
                        });
                    } else {
                        canvas.getObjects().map(function(o) {
                            if(o.id == 'done_text') {
                              o.set({
                                  opacity: 0
                              });
                            }  
                        });
                    }

                    var remote1 = $("#remote1").val();
                    var remote2 = $("#remote2").val();
                    var remote3 = $("#remote3").val();   

                    canvas.getObjects().map(function(o) {
                        if(o.id == "active_turn_user") {
                            canvas.remove(o);
                        }    
                    });         

                    if(next_turn == "1") {
                      var rectSelected = { left: 160, top: 195, fill: 'white', width: 293, height: 2 , id:"active_turn_user",lockMovementX:true,lockMovementY:true,stroke:"green",strokeWidth:10,};  
                        var rectSelectedobj = new fabric.Rect(rectSelected); 
                        canvas.add(rectSelectedobj);
                    } else if(next_turn == "2") {  
                        //console.log("testing");
                        var rectSelected = { left: 920, top: 195, fill: 'white', width: 293, height: 2 , id:"active_turn_user",lockMovementX:true,lockMovementY:true,stroke:"green",strokeWidth:10,};  
                        var rectSelectedobj = new fabric.Rect(rectSelected); 
                        canvas.add(rectSelectedobj);  
                    } else if(next_turn == "3") {
                        var rectSelected = { left: 920, top: 610, fill: 'white', width: 293, height: 2 , id:"active_turn_user",lockMovementX:true,lockMovementY:true,stroke:"green",strokeWidth:10,};  
                        var rectSelectedobj = new fabric.Rect(rectSelected); 
                        canvas.add(rectSelectedobj);
                    } else if(next_turn == "4") { 
                        var rectSelected = { left: 160, top: 610, fill: 'white', width: 293, height: 2 , id:"active_turn_user",lockMovementX:true,lockMovementY:true,stroke:"green",strokeWidth:10,};  
                        var rectSelectedobj = new fabric.Rect(rectSelected); 
                        canvas.add(rectSelectedobj);       
                    }  


                    var existingMarbles = new Array();
                    if(marble_data.length>0) {
                        Object.keys(marble_data).forEach(function(key) {
                            var marble = marble_data[key];
                            var marble_user_id = marble.user_id;
                            existingMarbles.push(marble.marble_id);
                            if(marble_user_id == '<?php echo $user_id ?>') {
                               return false;
                            }  

                            var previousLeft = '';
                            var previousTop = '';
                            canvas.getObjects().map(function(o) {
                                if(o.id == marble.current_position) {
                                  objectLeft = o.left+6;
                                  objectTop = o.top+5;  
                                }       
                                if(o.id == marble.previous_position) {
                                  previousLeft = o.left+6;
                                  previousTop = o.top+5;  
                                }
                            });    


                            var is_marble_exist = 0;
                            canvas.getObjects().map(function(o) {
                                if(o.type == marble.marble_id) {
                                      is_marble_exist = 1;
                                      /*o.set({
                                        left: objectLeft,
                                        top: objectTop
                                      });     
                                      o.setCoords({
                                        left: objectLeft,
                                        top: objectTop
                                      });*/
                                      o.animate('left', objectLeft, {
                                          duration: 1000,
                                          onChange: canvas.renderAll.bind(canvas),
                                          easing: fabric.util.ease['easeInQuad']
                                        });    
                                      o.animate('top', objectTop, {
                                          duration: 1000,
                                          onChange: canvas.renderAll.bind(canvas),
                                          easing: fabric.util.ease['easeInQuad']
                                        });    
                                      /*if(objectLeft>previousLeft) {
                                        var calculateDiff = objectLeft - previousLeft;
                                        o.animate('left', objectLeft, {
                                          duration: 1000,
                                          onChange: canvas.renderAll.bind(canvas),
                                          easing: fabric.util.ease['easeInQuad']
                                        });    
                                      }*/

                                }
                            });   
                      });        


                      console.log(existingMarbles);          

                      canvas.getObjects().map(function(o) {
                          if( !existingMarbles.includes(o.type) && o.type == 'yellow_3') { 
                                o.set({
                                  left: <?php echo $yellow_three_left_position; ?>,    
                                  top: <?php echo $yellow_three_top_position; ?>
                                });
                                o.setCoords({
                                  left: <?php echo $yellow_three_left_position; ?>,    
                                  top: <?php echo $yellow_three_top_position; ?>
                                });
                              } if(!existingMarbles.includes(o.type) && o.type == 'yellow_2') { 
                                  o.set({
                                    left: <?php echo $yellow_two_left_position; ?>,    
                                    top: <?php echo $yellow_two_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $yellow_two_left_position; ?>,    
                                    top: <?php echo $yellow_two_top_position; ?>
                                  });
                              } if(!existingMarbles.includes(o.type) && o.type == 'yellow_1') { 
                                  o.set({
                                    left: <?php echo $yellow_one_left_position; ?>,    
                                    top: <?php echo $yellow_one_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $yellow_one_left_position; ?>,    
                                    top: <?php echo $yellow_one_top_position; ?>
                                  });
                              } if(!existingMarbles.includes(o.type) && o.type == 'yellow_0') { 
                                  o.set({
                                    left: <?php echo $yellow_zero_left_position; ?>,    
                                    top: <?php echo $yellow_zero_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $yellow_zero_left_position; ?>,    
                                    top: <?php echo $yellow_zero_top_position; ?>
                                  });
                              } if(!existingMarbles.includes(o.type) && o.type == 'blue_3') { 
                                  o.set({
                                    left: <?php echo $blue_three_left_position; ?>,    
                                    top: <?php echo $blue_three_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $blue_three_left_position; ?>,    
                                    top: <?php echo $blue_three_top_position; ?>
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'blue_2') { 
                                  o.set({
                                    left: <?php echo $blue_two_left_position; ?>,    
                                    top: <?php echo $blue_two_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $blue_two_left_position; ?>,    
                                    top: <?php echo $blue_two_top_position; ?>
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'blue_1') { 
                                  o.set({
                                    left: <?php echo $blue_one_left_position; ?>,    
                                    top: <?php echo $blue_one_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $blue_one_left_position; ?>,    
                                    top: <?php echo $blue_one_top_position; ?>
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'blue_0') { 
                                  o.set({
                                    left: <?php echo $blue_zero_left_position; ?>,    
                                    top: <?php echo $blue_zero_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $blue_zero_left_position; ?>,    
                                    top: <?php echo $blue_zero_top_position; ?>
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'red_3') { 
                                  o.set({
                                    left: <?php echo $red_three_left_position; ?>,    
                                    top: <?php echo $red_three_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $red_three_left_position; ?>,    
                                    top: <?php echo $red_three_top_position; ?>
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'red_2') { 
                                  o.set({
                                    left: <?php echo $red_two_left_position; ?>,    
                                    top: <?php echo $red_two_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $red_two_left_position; ?>,    
                                    top: <?php echo $red_two_top_position; ?>
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'red_1') { 
                                  o.set({
                                    left: <?php echo $red_one_left_position; ?>,    
                                    top: <?php echo $red_one_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $red_one_left_position; ?>,    
                                    top: <?php echo $red_one_top_position; ?>
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'red_0') { 
                                  o.set({
                                    left: <?php echo $red_zero_left_position; ?>,    
                                    top: <?php echo $red_zero_top_position; ?>
                                  });
                                  o.setCoords({
                                    left: <?php echo $red_zero_left_position; ?>,    
                                    top: <?php echo $red_zero_top_position; ?>
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'green_3') { 
                                  o.set({
                                    left: <?php echo $green_three_left_position; ?>,    
                                    top: <?php echo $green_three_top_position; ?> 
                                  });
                                  o.setCoords({
                                    left: <?php echo $green_three_left_position; ?>,    
                                    top: <?php echo $green_three_top_position; ?> 
                                  });      
                              } if(!existingMarbles.includes(o.type) && o.type == 'green_2') { 
                                  o.set({
                                    left: <?php echo $green_two_left_position; ?>,    
                                    top: <?php echo $green_two_top_position; ?> 
                                  });
                                  o.setCoords({
                                    left: <?php echo $green_two_left_position; ?>,    
                                    top: <?php echo $green_two_top_position; ?> 
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'green_1') { 
                                  o.set({
                                    left: <?php echo $green_one_left_position; ?>,    
                                    top: <?php echo $green_one_top_position; ?>  
                                  });
                                  o.setCoords({
                                    left: <?php echo $green_one_left_position; ?>,    
                                    top: <?php echo $green_one_top_position; ?>  
                                  });  
                              } if(!existingMarbles.includes(o.type) && o.type == 'green_0') {          
                                  o.set({  
                                    left: <?php echo $green_zero_left_position; ?>,    
                                    top: <?php echo $green_zero_top_position; ?>  
                                  });
                                  o.setCoords({    
                                    left: <?php echo $green_zero_left_position; ?>,    
                                    top: <?php echo $green_zero_top_position; ?>  
                                  });      
                              }                                
                      });    
                    } else {
                      if(turn_user_id != '<?php echo $user_id ?>') {
                        resetToOriginalPosition();
                      }
                    }
                }      
            }
        });
    }

    Swal.fire({
      title: 'Do not refresh the page, once game is started',
      icon: 'info'
    })

    var is_your_turn = 0;  
    var channelUsersData;
    $(document).ready(function() {  


        var pusher = new Pusher('2a1428184af6e786468c', {
            cluster: 'ap2',
            encrypted: true
        });
        var channel = pusher.subscribe('dice_channel_<?php echo $room_id; ?>');
        var channelUsers = pusher.subscribe('room_users_<?php echo $room_id; ?>');
        var channelMarbleAccess = pusher.subscribe('marble_access_<?php echo $room_id; ?>');
        channel.bind('my_event',
          function(data) {
              console.log("event data");
              console.log(data);
              dice.dataset.side = data;
              dice.classList.toggle("reRoll");
        });

        channelUsers.bind('my_event',
          function(data) {
              console.log("users data");
              channelUsersData.push(data);
              console.log(data);
        });

        channelMarbleAccess.bind('my_event',
          function(data) {
              console.log("marble access");
              console.log(data);
              var current_user_id = '<?php echo $user_id; ?>';    
              console.log(current_user_id);
              var marble_access = JSON.parse(data[current_user_id]);
              console.log(marble_access);
              if(Object.keys(data).length > 0) {
                for(var n =0; n<marble_access.length; n++) {
                  var accessID = marble_access[n];
                  if(accessID == '1') {
                    canvas.getObjects().map(function(o) {  
                      if( n==0 && (o.type == 'yellow_0' || o.type == 'yellow_1' || o.type == 'yellow_2' || o.type == 'yellow_3') ) {
                        o.set({
                          lockMovementX: false,
                          lockMovementY: false
                        });
                        o.setCoords({
                          lockMovementX: false,
                          lockMovementY: false
                        }); 
                      } 
                      if( n==1 && (o.type == 'blue_0' || o.type == 'blue_1' || o.type == 'blue_2' || o.type == 'blue_3') ) {
                          o.set({
                            lockMovementX: false,
                            lockMovementY: false
                          });
                          o.setCoords({
                            lockMovementX: false,
                            lockMovementY: false
                          });
                      }
                      if( n== 2 && (o.type == 'red_0' || o.type == 'red_1' || o.type == 'red_2' || o.type == 'red_3') ) {
                          o.set({
                            lockMovementX: false,
                            lockMovementY: false
                          });
                          o.setCoords({
                            lockMovementX: false,
                            lockMovementY: false
                          });
                      }
                      if( n== 3 && (o.type == 'green_0' || o.type == 'green_1' || o.type == 'green_2' || o.type == 'green_3') ) {
                          o.set({ 
                            lockMovementX: false,
                            lockMovementY: false
                          });
                          o.setCoords({
                            lockMovementX: false,
                            lockMovementY: false
                          });
                      }
                    });
                  }
                  
                }
              }
              
              Swal.fire({
                icon: 'success',
                title: 'Game is started.....'
              })
        });
 

        let dice = document.getElementById('dice');
        var outputDiv = document.getElementById('diceResult');

        
        function rollDice() { 
            var current_turn = $("#turn_user_id").val();   
            if(current_turn != '<?php echo $user_id; ?>') {
                toastr.error("Please wait for your turn");
                return false;
            }
            /*if(is_your_turn == 0 || is_locked_dice == 1) {
                toastr.error("You already roll dice, please click on done button");
                return false; 
            }
            is_your_turn = 0;
            is_locked_dice = 1;*/

            let result = Math.floor(Math.random() * (6 - 1 + 1)) + 1;
            $("#dice_result").val(result);

            $.ajax({
                type : 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {"action": "WCP_VideoChat_Controller::roll_dice","user_id":"<?php echo $user_id; ?>","room_id":"<?php echo $room_id; ?>","result":result},
                dataType : 'json',
                success : function(msg) {

                }
            });
            
            dice.dataset.side = result;
            //dice.classList.toggle("reRoll");
            //console.log(result);
            //moveToEmptyCircle(canvas,result);
        }    

        dice.addEventListener("click", rollDice);      

    });    

    toastr.success('You are successfully joined the room');






        var base_url = 'https://vcaggravation-dev.serverdatahost.com:9441/'; 
        var mode = '0';     
        var room_name = 'test_<?php echo $room_id; ?>';
        mode = '';    
        var webrtc = null;
        var token = 0;
        var browser_name = '';
        var peerobj = null;
        screen_chrome_install = false;

        var count_video = 0;
        joinConference();
        function joinConference() {

            webrtc = new SimpleWebRTC({
                // the id/element dom element that will hold "our" video
                localVideoEl: 'localVideo',
                // the id/element dom element that will hold remote videos
                remoteVideosEl: '',
                nick: token,
                autoRequestMedia: true,
                debug: false,
                url: base_url,
                /*media: { 
                    audio: true,
                    video: {
                        mandatory: {
                          chromeMediaSource: 'desktop',
                          maxWidth: window.screen.width,
                          maxHeight: window.screen.height,
                          maxFrameRate: 3
                       }
                    }
                }*/
            });  

            // we have to wait until it's ready
            //alert(room_name);

            
            webrtc.on('readyToCall', function (e) {
                myPeerID = webrtc.connection.getSessionid();
                console.log("room name" + room_name);
                webrtc.joinRoom(room_name);
                console.log("log token =" + myPeerID);
            });
            // webrtc.on('startLocalVideo', function (sessionId) {
            //     console.log("sdsdsdsd");      
            // });  

            webrtc.on('videoAdded', function (video, peer) {
                var peerID = webrtc.getDomId(peer);
                console.log("video added");
                console.log(video.srcObject);
                console.log(peerID);
                peerID = peerID.replace('_video_incoming','');
                setTimeout(function(){      
                  addUser(peerID,video.srcObject,"remote",peerID);   
                }, 3000);
            });
            webrtc.on('videoRemoved', function (video, peer) {
                var peerID = webrtc.getDomId(peer);
                console.log("video removed = " + peerID);
                console.log("my peer id =" + myPeerID);
            });
            webrtc.on('channelMessage', function (peer, label, data) {
                if (label == "onReceiveSendToAll") {
                    var res = JSON.parse(data.payload.data);
                    var method = res.method
                    if (res.token == token) {
                        return;
                    }
                }
            });

            webrtc.on('createdPeer', function (peer) {
                //showNotifiction('New user joined');
                if (peer && peer.pc) {
                    peer.pc.on('iceConnectionStateChange', function (event) {
                        switch (peer.pc.iceConnectionState) {
                            case 'checking':

                                break;
                            case 'connected':
                            case 'completed': // on caller side

                                break;
                            case 'disconnected':

                                break;
                            case 'failed':
                                showNotifiction("Unable to connect remote peer.Please check your firewall settings");
                                break;
                            case 'closed':
                                //console.log('created peer--Connection closed.');
                                break;
                        }
                    });

                }

            });
        }

        function fullscreen(elem) {


            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            }
        }

        function installPlugin() {
            window.open('https://chrome.google.com/webstore/detail/ncgpiojdencehcbfemhkjabhceoikhik', '_blank');
        }

        function installFirefoxScreenCapturingExtension() {
            InstallTrigger.install({
                'Foo': {
                    URL: 'https://addons.mozilla.org/firefox/downloads/latest/wizertscreenshare/platform:5/addon-738696-latest.xpi?src=dp-btn-primary',
                    toString: function () {
                        return this.URL;
                    }
                }
            });
        }

        function sendToAll(data) {

            webrtc.sendDirectlyToAll('onReceiveSendToAll', 'onReceiveSendToAll', {
                data: data
            });
        }

        var chrome_ext_avail = false;

        function checkChromeExt(callbck) {
            if (!webrtc.capabilities.supportScreenSharing) {
                chrome_ext_avail = false;
            } else {
                chrome_ext_avail = true;
            }
            //return callbck(chrome_ext_avail);
            return callbck(false);
        }


        var ext_trigger_time = true; 

        function muteMyAudio() {
            webrtc.mute()
            showNotifiction('Your audio has been muted');
        }

        function unMuteMyAudio() {
            webrtc.unmute()
            showNotifiction('Your audio has been unmuted');
        }

        function pauseMyVideo() {
            webrtc.pauseVideo();
            showNotifiction('Your video has been disabled');
        }

        function resumeMyVideo() {
            webrtc.resumeVideo();
            showNotifiction('Your video has been enabled');
        }

        function muteVolume() {
            $('video').prop('muted', true);
            showNotifiction('Volume is muted');
        }

        function unmuteVolume() {
            $('video').prop('muted', false);
            $('#localVideo').prop('muted', true);
            showNotifiction('Volume is unmuted');
        }

        var isEdge = navigator.userAgent.indexOf('Edge') !== -1 && (!!navigator.msSaveOrOpenBlob || !!navigator.msSaveBlob);

        function getBrowserInfo() {
            var nVer = navigator.appVersion;
            var nAgt = navigator.userAgent;
            var browserName = navigator.appName;
            var fullVersion = '' + parseFloat(navigator.appVersion);
            var majorVersion = parseInt(navigator.appVersion, 10);
            var nameOffset, verOffset, ix;
            var screenshareok = 0;
            // In Opera, the true version is after 'Opera' or after 'Version'
            if ((verOffset = nAgt.indexOf('OPR')) !== -1) {
                browserName = 'Opera';
                fullVersion = nAgt.substring(verOffset + 6);

                if ((verOffset = nAgt.indexOf('Version')) !== -1) {
                    fullVersion = nAgt.substring(verOffset + 8);
                }
            }
            // In MSIE, the true version is after 'MSIE' in userAgent
            else if ((verOffset = nAgt.indexOf('MSIE')) !== -1) {
                browserName = 'IE';
                fullVersion = nAgt.substring(verOffset + 5);
            }
            // In Chrome, the true version is after 'Chrome'
            else if ((verOffset = nAgt.indexOf('Chrome')) !== -1) {
                browserName = 'Chrome';
                fullVersion = nAgt.substring(verOffset + 7);
                screenshareok = 1;
            }
            // In Safari, the true version is after 'Safari' or after 'Version'
            else if ((verOffset = nAgt.indexOf('Safari')) !== -1) {
                browserName = 'Safari';
                fullVersion = nAgt.substring(verOffset + 7);

                if ((verOffset = nAgt.indexOf('Version')) !== -1) {
                    fullVersion = nAgt.substring(verOffset + 8);
                }
            }
            // In Firefox, the true version is after 'Firefox'
            else if ((verOffset = nAgt.indexOf('Firefox')) !== -1) {
                browserName = 'Firefox';
                fullVersion = nAgt.substring(verOffset + 8);
                screenshareok = 1;
            }

            // In most other browsers, 'name/version' is at the end of userAgent
            else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
                browserName = nAgt.substring(nameOffset, verOffset);
                fullVersion = nAgt.substring(verOffset + 1);

                if (browserName.toLowerCase() === browserName.toUpperCase()) {
                    browserName = navigator.appName;
                }
            }

            if (isEdge) {
                browserName = 'Edge';
                // fullVersion = navigator.userAgent.split('Edge/')[1];
                fullVersion = parseInt(navigator.userAgent.match(/Edge\/(\d+).(\d+)$/)[2], 10);
            }

            // trim the fullVersion string at semicolon/space if present
            if ((ix = fullVersion.indexOf(';')) !== -1) {
                fullVersion = fullVersion.substring(0, ix);
            }

            if ((ix = fullVersion.indexOf(' ')) !== -1) {
                fullVersion = fullVersion.substring(0, ix);
            }

            majorVersion = parseInt('' + fullVersion, 10);

            if (isNaN(majorVersion)) {
                fullVersion = '' + parseFloat(navigator.appVersion);
                majorVersion = parseInt(navigator.appVersion, 10);
            }
            if (browserName == "Netscape") {
                browserName = "Internet Explorer";
            }

            if (screenshareok == 0) {
                //$('#screen-li').html('Screen share is only supported in chrome and firefox');
            }
            return {
                fullVersion: fullVersion,
                version: majorVersion,
                name: browserName
            };
        }

        browser_name = getBrowserInfo();
        browser_name = browser_name.name;


        var isChrome = !!navigator.webkitGetUserMedia;
        // DetectRTC.js - https://github.com/muaz-khan/WebRTC-Experiment/tree/master/DetectRTC
        // Below code is taken from RTCMultiConnection-v1.8.js (http://www.rtcmulticonnection.org/changes-log/#v1.8)
        var DetectRTC = {};
        (function () {

            var screenCallback;
            DetectRTC.screen = {
                chromeMediaSource: 'screen',
                getSourceId: function (callback) {
                    if (!callback)
                        throw '"callback" parameter is mandatory.';
                    screenCallback = callback;
                    window.postMessage('get-sourceId', '*');
                },
                isChromeExtensionAvailable: function (callback) {
                    if (!callback)
                        return;

                    if (DetectRTC.screen.chromeMediaSource == 'desktop')
                        return callback(true);

                    // ask extension if it is available
                    window.postMessage('are-you-there', '*');

                    setTimeout(function () {
                        if (DetectRTC.screen.chromeMediaSource == 'screen') {
                            callback(false);
                        } else
                            callback(true);
                    }, 2000);
                },
                getChromeExtensionStatus: function (callback) {
                }
            };

            // check if desktop-capture extension installed.
            if (window.postMessage && isChrome) {
                DetectRTC.screen.isChromeExtensionAvailable();
            }

        })();
        checkScreenStat();

        function checkScreenStat() {
            DetectRTC.screen.getChromeExtensionStatus(function (status) {
                var nAgt = navigator.userAgent;

                if (status == 'installed-enabled') {
                    DetectRTC.screen.chromeMediaSource = 'desktop';
                    checkScreenShare();
                } else if (nAgt.indexOf('Chrome') !== -1 && nAgt.indexOf('OPR') === -1) {
                    screen_chrome_install = false;
                }
            });
        }

        function checkScreenShare() {
            screen_chrome_install = true;
        }


        function register_tab_GUID() {
            // detect local storage available
            if (typeof (Storage) !== "undefined") {
                // get (set if not) tab GUID and store in tab session
                if (sessionStorage["tabGUID"] == null)
                    sessionStorage["tabGUID"] = tab_GUID();
                var guid = sessionStorage["tabGUID"];

                // add eventlistener to local storage
                window.addEventListener("storage", storage_Handler, false);

                // set tab GUID in local storage
                localStorage["tabGUID"] = guid;
            }
        }

        function storage_Handler(e) {
            // if tabGUID does not match then more than one tab and GUID
            if (e.key == 'tabGUID') {
                if (e.oldValue != e.newValue)
                    tab_Warning();
            }
        }

        function tab_GUID() {
            function s4() {
                return Math.floor((1 + Math.random()) * 0x10000)
                        .toString(16)
                        .substring(1);
            }
            return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                    s4() + '-' + s4() + s4() + s4();
        }

        register_tab_GUID();

    </script>               


