function getaddress(el) {
  var email = el.value;
  if (email == '') return;
  if (email.indexOf("@") == -1) return;
  if (el.validity && el.validity.valid) return;
  const request = new XMLHttpRequest();
  request.open("GET", "/getaddress.php?email=" + email, false);
  request.send(null);
  response = JSON.parse(request.response);
  if (response['address']) {
    el.value = response['address'];
    return;
  }
  if (response['error']) {
    alert(response['error']);
  }
  el.value='';
  return;
}
