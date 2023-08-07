<script>
  import { onMount } from "svelte";
  import { user } from '../js/stores.js';
  import cookieManager from "../js/classes/cookieManager.js";  

  onMount(() => {
    let manager = new cookieManager();
    
    let cookie = manager.getCookie('SESSION')

    if(!cookie || !(Array.isArray(cookie) && cookie.length === 3))
    {
      manager.createCookie('SESSION','[0,0,0]', 1);
    }
    else
    { // CAMBIAR ESTO
      let array = $user;
      array = cookie;
      user.set(array);
    }
  });
</script>
