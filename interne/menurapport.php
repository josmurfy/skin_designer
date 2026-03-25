
<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script> 

    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff" > 
<p>&nbsp;</p>
<table border="1" width="100%" bgcolor="FFFFFF">
<tbody>
<tr>
<td colspan="2" align="middle" valign="center" width="100%"><img src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg" height="200" /></td>
</tr>
<tr>
<td colspan="2" style="background-color: #030282; color: white;  text-align: center;"">
<h1><br>MENU RAPPORT</h1>
</td>
</tr>
<tr bgcolor="c0c0c0">

<td align="middle" valign="center" width="50%"><h2><br><a href="inventairereception.php" >Inventaire Reception</a></h2></td>
<td align="middle" valign="center" width="50%"><h2><br><a href="noinventaireusa.php" >Item NON Inventaire</a></h2></td>
</tr>

<tr bgcolor="ffffff">
<td align="middle" valign="center" width="50%"><h2><br><a href="inventairetransfert.php" >Inventaire Transfert</a></h2></td>
<td align="middle" valign="center" width="50%"><h2><br><a href="inventairedestroy.php" >Inventaire Salvage</a></h2></td>
</tr>
<tr bgcolor="c0c0c0">
<td align="middle" valign="center" width="50%"><h2><br><a href="inventaireinterne.php" >Inventaire pour USAGE INTERNE ou VIDE</a></h2></td>
<td></td>
</tr>

<tr bgcolor="ffffff">
<td align="middle" valign="center" width="50%"><h2><br><a href="verifinventaireusa.php" >Verification Inventaire non actif ou pas de location</a></h2></td>
<td align="middle" valign="center" width="50%"><h2><br><a href="inventairemodif.php" >Modification ROUGE/VERT</a></h2></td>
</tr>

</tbody>
</table>
</body>
</html>