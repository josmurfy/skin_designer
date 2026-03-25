<?php
 
?>
<html>
  <head>
    <title>Custom Search JSON API Example</title>
  </head>
  <body>
    <div id="content"></div>
    allo
    <script>
      function hndlr(response) {
      for (var i = 0; i < response.items.length; i++) {
        var item = response.items[i];
        // Make sure HTML in item.htmlTitle is escaped.
        document.getElementById("content").append(
          document.createElement("br"),
          document.createTextNode(item.htmlTitle)
        );
      }
    }
    </script>
    <script src="https://www.googleapis.com/customsearch/v1?key=AIzaSyBZaktoartb7qp7i3o3iW5O68nV04kZ1lg&cx=851651c4701664875:omuauf_lfve&q=cars&callback=hndlr">
    </script>
    <script async src="https://cse.google.com/cse.js?cx=851651c4701664875">
</script>
<div class="gcse-search"></div>
  </body>
</html>