<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title></title>
        
        <!-- Our CSS stylesheet file -->
        <link rel="stylesheet" href="assets/css/styles.css" />
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
        
        
        <!--[if lt IE 9]>
          <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <!-- Including The jQuery Library -->
		<script src="http://code.jquery.com/jquery-1.6.3.min.js"></script>
		
		<!-- Including the HTML5 Uploader plugin -->
		<script src="assets/js/jquery.filedrop.js"></script>
		
		<!-- Including GetURLParams -->
		<script src="assets/js/jquery.getURLParams.js"></script>
        
        <script type="text/javascript">
			<!--
			 
			$(document).ready(function () {
			 
			window.setTimeout(function() {
			    $(".preview").fadeTo(1500, 0).slideUp(500, function(){
			        $(".preview").remove(); 
			        $(".border").css('border-color','#999999'); 
			        var message = '<i class="glyphicon glyphicon-circle-arrow-down"></i>'+
				'<span class="message">Bild für pd-<span class="pn"></span>-<span class="c"></span> hineinlegen. <br /><i>(Einfach vom Computer hier rein ziehen drap & drop)</i></span>'
			
        			$(".border").html(message) 
			    });
			}, 5000);
			 
			
			 
			});
			//-->
		</script>
        
    </head>
    
    <body>
		
		<div id="dropbox">
			<div class="border">
				<i class="glyphicon glyphicon-circle-arrow-down"></i>
				<span class="message">Bild für pd-<span class="pn"></span>-<span class="c"></span> hineinlegen. <br /><i>(Einfach vom Computer hier rein ziehen drap & drop)</i></span>
			</div>
		
			</div>
        
        
		
		<!-- The main script file -->
        <script>
        	$('.pn').html($.getURLParam("p"));
        	$('.c').html($.getURLParam("c"));
        
        	var dropbox = $('#dropbox'),
        		message = $('.message', dropbox);
        	
        	dropbox.filedrop({
        		// The name of the $_FILES entry:
        		paramname:'pic',
        		
        		maxfiles: 5,
            	maxfilesize: 5,
        		url: 'post_file.php?number='+$.getURLParam("p")+'&color='+$.getURLParam("c"),
        		
        		uploadFinished:function(i,file,response){
        			$.data(file).addClass('done');
        			// response is the JSON object that post_file.php returns
        			//alert($.parseJSON(response));
        			
        			console.log(response);
        			$('#dropbox .border').css('border-color', '#4cae4c');
        			
        			var data = {
        				  data:{
        				  	  id : $.getURLParam('id'),
        	                  product_number : $.getURLParam('p'),
        	                  path : response.path,
        	                  ext : response.ext,
        	                  color: response.color,
        	                  status: response.status
        	                }
                    };
        			
        			//parent.$.colorbox.close();
        			parent.saveImageInDb(data);
        			
        			
        		},
        		
            	error: function(err, file) {
        			switch(err) {
        				case 'BrowserNotSupported':
        					showMessage('Your browser does not support HTML5 file uploads!');
        					break;
        				case 'TooManyFiles':
        					alert('Too many files! Please select 5 at most! (configurable)');
        					break;
        				case 'FileTooLarge':
        					alert(file.name+' is too large! Please upload files up to 5mb (configurable).');
        					break;
        				default:
        					
        					break;
        			}
        		},
        		
        		// Called before each upload is started
        		beforeEach: function(file){
        		
        			if(!file.type.match(/^image\//)){
        				alert('Only images are allowed!');
        				
        				// Returning false will cause the
        				// file to be rejected
        				return false;
        			}
        			file.name = 'test.jpg'
        		},
        		
        		uploadStarted:function(i, file, len){
        			createImage(file);
        		},
        		
        		progressUpdated: function(i, file, progress) {
        			$.data(file).find('.progress').width(progress);
        		}
            	 
        	});
        	
        	var template = '<div class="preview">'+
        						'<span class="imageHolder">'+
        							'<img />'+
        							'<span class="uploaded"><i class="glyphicon glyphicon-ok"></i></span>'+
        						'</span>'+
        						
        					'</div>'; 
        	
        	
        	function createImage(file){
        		
        
        		var preview = $(template), 
        			image = $('img', preview);
        			
        		var reader = new FileReader();
        		
        		image.width = 100;
        		image.height = 100;
        		
        		reader.onload = function(e){
        			
        			// e.target.result holds the DataURL which
        			// can be used as a source of the image:
        			
        			image.attr('src',e.target.result);
        		};
        		
        		// Reading the file as a DataURL. When finished,
        		// this will trigger the onload function above:
        		reader.readAsDataURL(file);
        		
        		message.hide();
        		$('#dropbox .border').html(preview);
        		
        		// Associating a preview container
        		// with the file, using jQuery's $.data():
        		
        		$.data(file,preview);
        	}
        
        	function showMessage(msg){
        		//message.html(msg);
        	}
        
        </script>
    
    </body>
</html>

