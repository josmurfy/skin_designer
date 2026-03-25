<style>
    
    video#bgvid {
    position: fixed; right: 0; bottom: 0;
    min-width: 100%; min-height: 100%;
    width: auto; height: auto; 
    z-index: -100;
    background: url('https://phoenixliquidation.ca/image/catalog/newintro.png') no-repeat;
    background-size: cover;
    }
    video { display: block; }

    .myButton{
    background: url('https://phoenixliquidation.ca/image/catalog/newintro.png') no-repeat;
    margin:0 auto;
    width:300px;
    height:300px;

    }
    
    
    
/* Style the video: 100% width and height to cover the entire window */
#myVideo {
  position: fixed;
  right: 0;
  bottom: 0;
  min-width: 100%;
  min-height: 100%;
}

/* Add some content at the bottom of the video/page */
.contentintro {
  position: fixed;
 
  background: rgba(0, 0, 0, 0.2);
  color: #f1f1f1;
  width: 100%;
  height: 50%;
  padding: 20px;
}

/* Style the button used to pause/play the video */

 </style>
 
 <section class="callout">
    <div class="video-bg">
			<!-- The video -->
		<video autoplay loop muted id="bgvid">
		<source src="https://phoenixliquidation.ca/image/catalog/newintro.webm" type="video/webm">
		  <source src="https://phoenixliquidation.ca/image/catalog/newintro.mp4" type="video/mp4">
		</video>

    </div>
    <div class="video-overlay"></div>
    <div class="">
        <!-- Our callout content goes here -->
        <img src=" <?echo $image_front;?>" title="" alt="" style="width:100%" class="img-responsive" /></a>
         <?echo $text_maintext;?>
        <br>
       
    </div>

</section>
 