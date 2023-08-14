class cookieManager
{

  createCookie(name, value, days)
  {
    let expires = "";
    if (days)
    {
      const date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toUTCString();
      value = value;
    }
    document.cookie = name + "=" + JSON.stringify(value) + expires + "; path=/";
  }
  
  getCookie(name) {
    const nameEQ = name + "=";
    const cookies = document.cookie.split(';');
  
    for (let i = 0; i < cookies.length; i++) {
      let cookie = cookies[i];
      while (cookie.charAt(0) === ' ') {
        cookie = cookie.substring(1, cookie.length);
      }
      if (cookie.indexOf(nameEQ) === 0) {
        const cookieValue = cookie.substring(nameEQ.length, cookie.length);
        return JSON.parse(cookieValue);
      }
    }
    return false;
  }

}

export default cookieManager;