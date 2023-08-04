<script>
  import { onMount } from "svelte";
  import { user } from '../js/stores.js';
  import cookieManager from "../js/utilities/cookieManager.js";  

  onMount(() => {
    let manager = new cookieManager();
    
    let cookie = manager.getCookie('SESSION');

    if (!cookie)
    {
      manager.createCookie('SESSION','[0,0,0]', 1);
    }
  });
</script>
