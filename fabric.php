<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <script src="fabric.js"></script>
  <script type='text/javascript' src='https://cdn.scaledrone.com/scaledrone.min.js'></script>
  <!-- <script src="script.js"></script> -->
</head> 
<body> 

 
	<video id="localVideo" width="300" height="400"  style="height: auto;display: none" autoplay muted></video>
	<video id="remoteVideo1" width="300" height="400" style="height: auto;display: none" autoplay></video>    
	<video id="remoteVideo2" width="300" height="200" style="height: auto;display: none" autoplay></video>  
	<video id="remoteVideo3" width="300" height="200" style="height: auto;display: none" autoplay></video>  
	<canvas id="c" style="border: 1px solid black; position: relative; "></canvas> 	      
        
	      
  	<script>

  	(function() {	 

	  		var canvas = this.__canvas = new fabric.Canvas('c',{ selection: true });

	  		var video1El = document.getElementById('localVideo');
	  		var remoteVideo1El = document.getElementById('remoteVideo1');
	  		var remoteVideo2El = document.getElementById('remoteVideo2');
	  		var remoteVideo3El = document.getElementById('remoteVideo3');    
	  		var video1 = new fabric.Image(video1El, {        
				  left: 310, //310 0
				  top: 0,      
				  originX: 'center',
				  originY: 'center'
			});  
			canvas.add(video1);

			var remoteVideo1 = new fabric.Image(remoteVideo1El, {        
				  left: 1070,  
				  top: 0,  
				  originX: 'center',
				  originY: 'center'
			});  
			canvas.add(remoteVideo1);      

			var remoteVideo2 = new fabric.Image(remoteVideo2El, {        
				  left: 310,  
				  top: 490,  
				  originX: 'center',
				  originY: 'center'  
			});  
			canvas.add(remoteVideo2);      

			var remoteVideo3 = new fabric.Image(remoteVideo3El, {        
				  left: 1070,  
				  top: 490,  
				  originX: 'center',
				  originY: 'center'
			});  
			canvas.add(remoteVideo3);          
			

			/*************** video streaming *************/
			// Generate random room name if needed
			if (!location.hash) {
			  location.hash = Math.floor(Math.random() * 0xFFFFFF).toString(16);
			}
			const roomHash =  "123"; //location.hash.substring(1);

			// TODO: Replace with your own channel ID
			const drone = new ScaleDrone('2xmbUiTsqTzukyf7');
			// Room name needs to be prefixed with 'observable-'
			const roomName = 'observable-' + roomHash;
			const configuration = {
			  iceServers: [{
			    urls: 'stun:stun.l.google.com:19302'
			  }]
			};
			let room;
			let pc;  


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
			  // We're connected to the room and received an array of 'members'
			  // connected to the room (including us). Signaling server is ready.
			  room.on('members', members => {
			    console.log('MEMBERS', members);
			    // If we are the second user to connect to the room we will be creating the offer
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

			  // When a remote stream arrives display it in the #remoteVideo element
			  pc.onaddstream = event => { //onaddstream  
			    remoteVideo1.srcObject = event.stream;          

			    console.log(event.stream);            
 
			    remoteVideo1El.srcObject = event.stream;
			    canvas.add(remoteVideo1);  
			    remoteVideo1.moveTo(0); // move webcam element to back of zIndex stack
			    remoteVideo1.getElement().play();
			  };

			  navigator.mediaDevices.getUserMedia({
			    audio: true,
			    video: true,
			  }).then(stream => {
			    // Display your local video in #localVideo element
			    localVideo.srcObject = stream;
			    // Add your stream to be sent to the conneting peer
			    pc.addStream(stream);


			    video1El.srcObject = stream;
			    canvas.add(video1);
			    video1.moveTo(0); // move webcam element to back of zIndex stack
			    video1.getElement().play();   


			  }, onError);

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







      

              
            
	  		var rect1 = { left: 160, top: 0, stroke:"#FFC000",strokeWidth:3,fill:'',width: 300, height: 200, id:"user_one",lockMovementX:true,lockMovementY:true };
              
	  		var rect2 = { left: 920, top: 0,fill: 'white', width: 300, height: 200 , id:"user_two",lockMovementX:true,lockMovementY:true,stroke:"#305496",strokeWidth:3 };
	  		var rect3 = { left: 160, top: 390, fill: 'white', width: 300, height: 200 , id:"user_three",lockMovementX:true,lockMovementY:true,stroke:"#A9C099",strokeWidth:3};
	  		var rect4 = { left: 920, top: 390, fill: 'white', width: 300, height: 200 , id:"user_four",lockMovementX:true,lockMovementY:true,stroke:"#DE7E7E",strokeWidth:3,};  

	  		var rect1obj = new fabric.Rect( rect1 )
	  		var rect2obj = new fabric.Rect( rect2 ) 
	  		var rect3obj = new fabric.Rect( rect3 )
	  		var rect4obj = new fabric.Rect( rect4 )



	  		canvas.add(rect1obj);   
	  		canvas.add(rect2obj);       
	  		canvas.add(rect3obj);
	  		canvas.add(rect4obj);

	  		/***************** left top left bar **************/
	  		fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "yellow_0";
				myImg.left = 120;
				myImg.top = 0;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "yellow_1";
				myImg.left = 120;
				myImg.top = 50;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "yellow_2";
				myImg.left = 120;
				myImg.top = 100;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "yellow_3";
				myImg.left = 120;
				myImg.top = 150;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			/**************** left top bottom bar **************/
			fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "yellow_4";
				myImg.left = 170;
				myImg.top = 210;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 224,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 284,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 344,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 404,lockMovementX:true,lockMovementY:true }));

			/**************** left top right bar **************/

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 0, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 42, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 84, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 122, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 164, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 206, left: 470,lockMovementX:true,lockMovementY:true }));

			/**************** right top right bar **************/

			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_0";
				myImg.left = 1240;
				myImg.top = 0;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_1";
				myImg.left = 1240;
				myImg.top = 50;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_2";
				myImg.left = 1240;
				myImg.top = 100;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 1240;
				myImg.top = 150;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			/**************** right top left bar **************/
			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 885;
				myImg.top = 0;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 42, left: 885,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 84, left: 885,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 122, left: 885,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 164, left: 885,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 206, left: 885,lockMovementX:true,lockMovementY:true }));

			/**************** right top bottom bar **************/
			
			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 930,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 980,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 1030,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 1080,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 210, left: 1130,lockMovementX:true,lockMovementY:true }));

			/******************** top middle bar **************/
			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 0, left: 570,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 0, left: 670,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 0, left: 770,lockMovementX:true,lockMovementY:true }));		

			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 670;
				myImg.top = 42;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 670;
				myImg.top = 84;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 670;
				myImg.top = 126;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-blu-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 670;
				myImg.top = 168;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});


			/***************** left bottom left bar **************/
	  		fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "green_0";
				myImg.left = 120;
				myImg.top = 570;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "green_1";
				myImg.left = 120;
				myImg.top = 520;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "green_2";
				myImg.left = 120;
				myImg.top = 470;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "green_3";
				myImg.left = 120;
				myImg.top = 420;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			/**************** left bottom top bar **************/

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 170,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 224,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 284,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 344,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 404,lockMovementX:true,lockMovementY:true }));


			/**************** left bottom right bar **************/

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 397, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 439, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 481, left: 470,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 523, left: 470,lockMovementX:true,lockMovementY:true }));

			fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "green_3";
				myImg.left = 470;
				myImg.top = 565;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});    


			/**************** right bottom right bar **************/

			fabric.Image.fromURL('flat-red-010.png', function(myImg) {
				myImg.id = "red_0";
				myImg.left = 1240;
				myImg.top = 570;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-red-010.png', function(myImg) {
				myImg.id = "red_1";
				myImg.left = 1240;
				myImg.top = 520;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-red-010.png', function(myImg) {
				myImg.id = "red_2";
				myImg.left = 1240;
				myImg.top = 470;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-red-010.png', function(myImg) {
				myImg.id = "red_3";
				myImg.left = 1240;  
				myImg.top = 420;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			/**************** right bottom left bar **************/

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 885,lockMovementX:true,lockMovementY:true }));  

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 397, left: 885,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 439, left: 885,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 481, left: 885,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 521, left: 885,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 563, left: 885,lockMovementX:true,lockMovementY:true }));


			/******************** bottom middle bar **************/
			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 563, left: 570,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 563, left: 670,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 563, left: 770,lockMovementX:true,lockMovementY:true }));		

			fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 670;
				myImg.top = 521;   //42 add
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 670;
				myImg.top = 479;
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 670;
				myImg.top = 437;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-grn-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 670;
				myImg.top = 395;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});


			/**************** right bottom top bar **************/  
			
			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 930,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 980,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 1030,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 1080,lockMovementX:true,lockMovementY:true }));

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 355, left: 1130,lockMovementX:true,lockMovementY:true }));

			/**************** middle right bar **************/  
   
			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 320, left: 1130,lockMovementX:true,lockMovementY:true }));      

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 280, left: 1130,lockMovementX:true,lockMovementY:true }));      

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 245, left: 1130,lockMovementX:true,lockMovementY:true }));     


			/**************** middle left bar **************/  
   
			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 320, left: 170,lockMovementX:true,lockMovementY:true }));      

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 280, left: 170,lockMovementX:true,lockMovementY:true }));      

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 245, left: 170,lockMovementX:true,lockMovementY:true }));     

			/**************** middle middle bar **************/  

			fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 224;
				myImg.top = 280;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 284;
				myImg.top = 280;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 344;
				myImg.top = 280;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-yel-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 404;
				myImg.top = 280;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});


			fabric.Image.fromURL('flat-red-010.png', function(myImg) {
				myImg.id = "blue_3";
				myImg.left = 1080;   
				myImg.top = 280;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-red-010.png', function(myImg) {  
				myImg.id = "blue_3";
				myImg.left = 1030;   
				myImg.top = 280;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			fabric.Image.fromURL('flat-red-010.png', function(myImg) {  
				myImg.id = "blue_3";
				myImg.left = 980;   
				myImg.top = 280;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});


			fabric.Image.fromURL('flat-red-010.png', function(myImg) {  
				myImg.id = "blue_3";
				myImg.left = 930;   
				myImg.top = 280;	
				myImg.lockMovementX = true;
				myImg.lockMovementY = true;
			 	canvas.add(myImg); 
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});

			canvas.add(new fabric.Circle({ radius: 12, fill: 'white',stroke:"black",strokeWidth:2, top: 280, left: 670,lockMovementX:true,lockMovementY:true }));     
	  		

			var objectsLength = [];
	  		var objs = canvas.getObjects().map(function(o) {
	  			//console.log(o);
	  			objectsLength.push({left:o.left,width:o.width,top:o.top,height:o.height,}); 
			  	return o.set('active', true);
			});               

			canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = o.hasRotatingPoint = false; }); 
 
			/*fabric.Image.fromURL('https://img.icons8.com/windows/2x/macos-close.png', function(myImg) {
				myImg.id = "bullet";
			 	canvas.add(myImg);  
			 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
			});*/      

			canvas.on({
			    'object:moving': function(e) {
			    	//console.log(e.target.canvas);	  
			    },
			    'object:modified': function(options) {

			      	
			    },
			    'object:moved': function(e) {
			    	return false;           
    				console.log(e.target);
    				//console.log(e.target.left,e.target.top);
    				//console.log(objectsLength);

    				var movedClientX = e.target.left;	
					var movedClientY = e.target.top;
					
					var is_element_exist = 0;
					for(var n=0; n<objectsLength.length; n++) {
						var objectLeft =  objectsLength[n].left;
						var objectWidth = objectsLength[n].width;
						var objectTop = objectsLength[n].top;
						var objectHeight = objectsLength[n].height;
						//var cacheTranslationX = objectsLength[n].cacheTranslationX;
						//var cacheTranslationY = objectsLength[n].cacheTranslationY;

						var totlaHeightCompare = objectHeight+objectTop;
						var totlaWidthCompare = objectWidth+objectLeft;

						if( (movedClientX>=objectLeft && movedClientX<=totlaWidthCompare) && (movedClientY>=objectTop && movedClientY<=totlaHeightCompare)  ) {

							var centerX = objectLeft + (objectWidth/2.5);
							var centerY = objectTop + (objectHeight/2.5);
							var imageURL = 'https://img.icons8.com/windows/2x/macos-close.png';
							fabric.Image.fromURL(imageURL, function(myImg) {
							 	canvas.add(myImg.set({left: centerX, top: centerY})); 
							 	canvas.forEachObject(function(o){ o.hasBorders = o.hasControls = false; }); 
							});
							is_element_exist = 1;
						}    
					}

					if(is_element_exist == 0) {    
						canvas.getObjects().map(function(o) {
							if(o.id == "bullet") {
						        o.set({
						          left: 10,
						          top: 10
						        });
						        o.setCoords({
						          left: 10,
						          top: 10
						        });
							}
						});
					}
					

			    },
			 });

			
			/************************************/	

			//canvas.renderAll();

			window.addEventListener('resize', resizeCanvas, false);

			
			  function resizeCanvas() {
			  		var innerWidth = window.innerWidth - 50;

			  		//console.log(window.innerWidth);
			  		//console.log(window.innerHeight);


				    canvas.setHeight(window.innerHeight);
				    canvas.setWidth(innerWidth);



				    var originalWidth = 1300;
				    var originalHeight = 980;

				    canvas.setZoom(innerWidth/originalWidth);
					//canvas.setWidth(originalWidth * canvas.getZoom());
					//canvas.setHeight(originalHeight * canvas.getZoom());

				    //canvas.calcOffset();
				    canvas.renderAll(); //innerWidth
				    //console.log("zoom:"+canvas.getZoom());

				    
			  }     


			fabric.util.requestAnimFrame(function render() {
					var innerWidth = window.innerWidth - 50;

			  		//console.log(window.innerWidth);
			  		//console.log(window.innerHeight);    

				    canvas.setHeight(window.innerHeight);
				    canvas.setWidth(innerWidth); 

				    var originalWidth = 1300;
				    var originalHeight = 980;

				    canvas.setZoom(innerWidth/originalWidth);
			  		canvas.renderAll();
					  fabric.util.requestAnimFrame(render);
			});    


			  // resize on init
			  //resizeCanvas();
	  			
	})();  

  	</script>	

  	

</body>
</html>       