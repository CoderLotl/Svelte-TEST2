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
      if (response.status === 200) {
        let data = await response.json();
        logged.set(true);
        user.set(data.user);
      }
    } catch (error) {
      console.log(`EXCEPTION: ${error}`);
    }
  });
</script>
