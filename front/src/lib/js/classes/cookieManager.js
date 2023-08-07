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
    document.cookie = name + "=" + value + expires + "; path=/";
  }

  getCookie(name)
  {
    const cookieString = document.cookie;
    const cookies = cookieString.split(";").map((cookie) => cookie.trim());
    for (const cookie of cookies)
    {
      const [cookieName, cookieValue] = cookie.split("=");
      if(cookieName === name)
      {
        return cookieValue;
      }
    }
    return false;
  }
}

export default cookieManager;