---
title: 'Our Quarry'
images:
  - https://bq.imgix.net/driveway/afirst-111.jpg
  - https://bq.imgix.net/driveway/afirst-111.jpg?w=100&dpr=2&ch=Width,DPR
  - https://bq.imgix.net/driveway/afirst-111.jpg?w=100&dpr=2&ch=Width,DPR
  - https://bq.imgix.net/driveway/afirst-111.jpg?w=200&dpr=2&ch=Width,DPR
  - https://bq.imgix.net/driveway/afirst-111.jpg?ch=Width&DPR&w=300&dpr=2
  - https://bq.imgix.net/driveway/afirst-111.jpg?w=400&dpr=2&ch=Width,DPR
  - https://bq.imgix.net/driveway/afirst-111.jpg?w=650" srcset="https://bq.imgix.net/driveway/afirst-111.jpg?w=650&1x=null, https://bq.imgix.net/driveway/afirst-111.jpg?w=650&fit=max&q=40&dpr=2&2x=null, https://bq.imgix.net/driveway/afirst-111.jpg?w=650&fit=max&q=20&dpr=3&3x=null


nav_title: 'our quarry'
template: gallery
fieldset: gallery
id: 57649ac5-6899-403e-a2e9-83836580202f
---
<h4> From this raw state it is sorted by mechanical means, that separates rock from the pit run material greater than four inches. The remaining material is then sent to a crusher and screen decks to produce the desired sizes and grades required for projects.</h4>
<h4>The result is a 100% granite aggregate.</h4>
<p>
<img src="https://bq.imgix.net/misc/saw-00240.jpg">
</p>
</br>
<h4>Brown&#8217;s Quarry operates a diamond wire saw which utilizes the most advanced granite cutting technology. Stone is sawn on the beds to specific heights &amp; lengths. Alongside our cut granite products, we offer a complete line of natural stone in a variety of colors and thickness for a number of creative applications.</h4>

// Demonstrate the use of the `fm=json` parameter to resize images
// to a certain aspect ratio, using ES6.
<script>
var ratio = 16 / 9;
var maxSize = 300;

var placeImages = function placeImages() {
  jQuery('.imgix-item').each(function (i, value) {
    var $elem = jQuery(value);
    // We pull down the image specific by the 'data-src' attribute
    // of each .imgix-item, but append the "?fm=json" query string to it.
    // This instructs imgix to return the JSON Output Format instead of
    // a manipulated image.
    var url = new imgix.URL($elem.attr('data-src'), { fm: "json" }).getUrl();

    jQuery.ajax(url).success(function (data) {
      var newWidth = void 0,
          newHeight = void 0;

      // Next, we compute the new height/width params for
      // each of our images.
      if (data.PixelHeight > data.PixelWidth) {
        newHeight = maxSize;
        newWidth = Math.ceil(newHeight / ratio);
      } else {
        newWidth = maxSize;
        newHeight = Math.ceil(newWidth / ratio);
      }

      // Now, we apply these to our actual images, setting the 'src'
      // attribute for the first time.
      $elem.get(0).src = new imgix.URL($elem.attr('data-src'), {
        w: newWidth,
        h: newHeight,
        fit: "crop"
      }).getUrl();
    });
  });
};

jQuery(document).ready(placeImages);
</script>
body{  text-align: center;}image-container {  width: auto;  display: inline-block;  vertical-align: top;  max-width: 1280px;  height: auto;  margin: 20px 0 20px 20px;    img {    max-width: 100%;  }}h1, p{  font-family:'Helvetica Neue', Helvetica, Arial, sans;  max-width: 480px;  margin: 1em auto 1em auto;  text-align: left;  line-height: 1.4;  color: #333;}h1{  line-height: 1.2;  margin-top: 2em;  margin-bottom: 0;}code{  font-family: "Lucida Console",monaco,mono;  color: #4b7498;}

<div><h1>Demonstrating <code>fm=json</code> parameter to set aspect ratios based on original image sizes using imgix.</h1>    <br></br>  <p>Originally, we cropped all of our images to fit within the same aspect ratio. Originals were set to <code>w=300</code>, <code>h=200</code>, and <code>fit=crop</code>.</p>  <image-container>    <img src="https://bq.imgix.net/driveway/afirst-111.jpg?w=300&h=200&fit=crop">  </image-container>  <image-container>    <img src="https://bq.imgix.net/driveway/afirst-111.jpg?w=300&h=200&fit=crop">  </image-container>  <image-container>    <img src="https://bq.imgix.net/driveway/afirst-111.jpg?w=300&h=200&fit=crop">  </image-container>  <image-container>    <img src="https://bq.imgix.net/driveway/afirst-111.jpg?w=300&h=200&fit=crop">  </image-container>  <p>Eventually, we decided that we wanted to maintain the aspect ratios of the source images. We can now do that using the <a href="https://www.imgix.com/docs/reference/format#param-fm">imgix JSON Output Format</a>. In this pen, we use JavaScript to read metadata about an image then set a max dimensions with the longest edge of 300px and a 16:9 ratio.</p>  <image-container>    <img data-src="https://bq.imgix.net/driveway/afirst-111.jpg" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="imgix-item">  </image-container>  <image-container>    <img data-src="https://bq.imgix.net/driveway/afirst-111.jpg" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="imgix-item">  </image-container>  <image-container>    <img data-src="https://bq.imgix.net/driveway/afirst-111.jpg" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="imgix-item">  </image-container>  <image-container>    <img data-src="https://bq.imgix.net/driveway/afirst-111.jpg" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="imgix-item">  </image-container><p>  Without storing any information server-side, we're able to build rich displays of our images based on their natural aspect ratios. This opens up a whole new way of working with images on imgix.</p><p>imgix is a hosted service that takes your existing static images and turns them into dynamic, responsive images that you can deliver to any device, under any conditions. It’s free to sign up at <a href="https://imgix.com">imgix.com</a>.</p></div>
