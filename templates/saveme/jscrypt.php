<html>
<head>
<title>Jscrypt</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META NAME="ROBOTS" CONTENT="NONE">

<script type="text/javascript" src="/include/seriti/javascript.js"></script>
<link href="/include/seriti/style.css" rel="stylesheet" type="text/css">

<script language="javascript" type="text/javascript" src="/include/jscrypt/jscrypt.js"></script>  
<script language="javascript" type="text/javascript" src="/include/jscrypt/aes.js"></script>      
<script language="javascript" type="text/javascript" src="/include/jscrypt/aesprng.js"></script>  
<script language="javascript" type="text/javascript" src="/include/jscrypt/md5.js"></script>  
<script language="javascript" type="text/javascript" src="/include/jscrypt/utf-8.js"></script> 
<script language="javascript" type="text/javascript" src="/include/jscrypt/entropy.js"></script>   
<script language="javascript" type="text/javascript" src="/include/jscrypt/armour.js"></script>  
<script language="javascript" type="text/javascript" src="/include/jscrypt/lecuyer.js"></script>  

<script type="text/javascript" language="JavaScript">
<!-- 
//  Our onLoad handler kicks off the collection of entropy
function nowLoaded() {    
ce();                    // Add time we got here to entropy
mouseMotionEntropy(60);     // Initialise collection of mouse motion entropy
}
// -->
</script>
</head>

<body onload="nowLoaded();">

<!--  seriti header  stuff -->
<?php 
 echo '<h1>'.MODULE_LOGO.' Organiser module: Javascript local encryption <a href="javascript:toggle_display(\'info_div\')">info</a></h1>';
 
?>
<div id="edit_div">
<!-- end header stuff -->            

<p id="info_div" style="display:none;">
<a href="javascript:toggle_display('info_div')">X</a>
This facility does not store your key in a cookie, or transmit it to the server, or store it in any way!
You can use any password or phrase up 1000 characters long as a key. Alternatively the [Generate] facility will create a 
very secure key for you. NB1: This facility is intended for situations where you want to store very sensitive text information
such as bank account details, credit card numbers...etc. Place the text you want to encrypt in "Plain text" block on left,
then enter your key and click [Encrypt] button, then cut/paste Cipher text from block on right into any standard text notes block
and save the cipher text(which is then encrypted again using the standard organiser key).
To decrypt cipher text, cut/paste text from text note(or anywhere else you may have stored it) into cipher text block on right,
enter your key and click [Decrypt] button to see decrypted plain text in left block.<br/>
NB: IF YOU FORGET YOUR KEY THERE IS NO POSSIBLE WAY TO RECOVER IT OR ANY TEXT ENCRYPTED WITH IT!
<a href="javascript:toggle_display('info_div')">OK I get it!</a>
</p>


<table width="100%" cellpadding="0" cellspacing="8">
  <tr>
    <td colspan="2">

      <form name="key" action="#" onsubmit="return false;">
      <b>Key </b>
      <input type="text" name="text" size="30" maxlength="1024"
      class="userinput"
      onfocus="ce();" onblur="ce();" onkeydown="ce();" />

      <input type="radio" checked="checked" name="keytype" />&nbsp;Text
      &nbsp;
      <input type="radio" name="keytype" />&nbsp;Hex
      &nbsp;
      <input type="button" value=" Generate " onclick="Generate_key();" />
      &nbsp;
      <input type="button" value=" Clear " onclick="document.key.text.value = '';" />
      </form>

    </td>
  </tr>
  <tr>
    <td valign="top">

      <form name="plain" action="#" onsubmit="return false;">
      <b>Plain Text...</b>
      <br />
      <textarea name="text" rows="16" cols="48" class="userinput"
      onfocus="ce();" onblur="ce();" onkeydown="ce();">
      </textarea>
      <br />
      <input type="button" name="encrypt" value=" Encrypt " onclick="Encrypt_text();" />
      &nbsp;
      <input type="button" value=" Clear " onclick="document.plain.text.value = '';" />
      &nbsp;
      <input type="button" value=" Select " onclick="document.plain.text.select();" />
      <br />

      <input type="radio" checked="checked" name="encoding" />&nbsp;Codegroup
      &nbsp;
      <input type="radio" name="encoding" />&nbsp;Hexadecimal
      &nbsp;
      <input type="radio" name="encoding" />&nbsp;Base&nbsp;64

      </form>

    </td>
    <td valign="top">

      <form name="cipher" action="#" onsubmit="return false;">
      <b>Cipher Text...</b>
      <br />
      <textarea name="text" rows="16" cols="48" class="userinput"
      onfocus="ce();" onblur="ce();" onkeydown="ce();">
      </textarea>
      <br />
      <input type="button" name="decrypt" value=" Decrypt " onclick="Decrypt_text();" />
      &nbsp;
      <input type="button" value=" Clear " onclick="document.cipher.text.value = '';" />
      &nbsp;
      <input type="button" value=" Select " onclick="document.cipher.text.select();" />
      <br />

      </form>
    </td>
  </tr>
</table>

<!-- seriti footer stuff -->
</div>

</body>
</html>