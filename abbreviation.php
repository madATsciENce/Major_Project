<html>
<head>
<script>
function showResult(str) {
  if (str.length==0) {
    document.getElementById("livesearch").innerHTML="";
    document.getElementById("livesearch").style.border="0px";
    return;
  }
  var xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("livesearch").innerHTML=this.responseText;
      document.getElementById("livesearch").style.border="1px solid #A5ACB2";
    }
  }
  xmlhttp.open("GET","hotels10.php?q="+str,true);
  xmlhttp.send();
}
</script>
</head>
<body>

<form>
<input type="text" size="30" id="name" onkeyup="showResult(this.value)">
<div id="livesearch"></div>
</form>

</body>
</html>