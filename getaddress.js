var lastResolvedAddress;

function getaddress(el) {
  var email = el.value;
  if (email == '') {
    document.getElementById('recipient_email').value = '';
    return;
  }
  if (email.indexOf("@") == -1) {
    document.getElementById('recipient_email').value = '';
    return;
  }
  if (el.validity && el.validity.valid) return;
  const request = new XMLHttpRequest();
  request.open("GET", "/getaddress.php?email=" + email, false);
  request.send(null);
  response = JSON.parse(request.response);
  if (response['address']) {
    lastResolvedAddress = response['address'];
    document.getElementById('recipient_email').value = email;
    el.value = response['address'];
    return;
  }
  if (response['error']) {
    alert(response['error']);
  }
  el.value='';
  return;
}

function getaddress2() {
  var email = document.getElementById('recipient_email').value;
  var address = document.getElementById('recipient');
  const request = new XMLHttpRequest();
  request.open("GET", "/getaddress.php?email=" + email, false);
  request.send(null);
  response = JSON.parse(request.response);
  if (response['address']) {
    lastResolvedAddress = response['address'];
    address.value = response['address'];
    return;
  }
  if (response['error']) {
    alert(response['error']);
  }
  address.value='';
  return;
}
