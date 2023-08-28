<script>
  import { onMount } from "svelte";
  import { params } from "../js/init.js";
  import { logged, user } from "../js/stores.js";

  onMount(async () => {
    try {
      const response = await fetch(params["home"] + "/auth/validate", {
        method: "GET",
        credentials: "include",
      });
      const data = await response.json();
      if (response.status === 200) {        
        user.set(data.user);
        logged.set(true);
      }
      else
      {
        console.log('No way');
      }
    } catch (error) {
      console.log(`EXCEPTION: ${error}`);
    }
  });
</script>
