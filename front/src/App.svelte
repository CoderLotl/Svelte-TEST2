<script>
  import { auth } from "./lib/svelte/Init.svelte";
  import Login from "./lib/svelte/Login.svelte";
  import Main from "./lib/svelte/Main.svelte";
  import { params } from "./lib/js/init.js";
  import { logged, user } from "./lib/js/stores.js";

  async function auth_()
  {
    const response = await auth();
    if (response.status === 200) {        
        const data = await response.json();
        user.set(data.user);
        logged.set(true);
        return true;
      } else {
        logged.set(false);
        user.set(null);
        return false;
      }
  }
  
  let authentication = auth_();
  authentication;
</script>

{#await authentication then}
  {#if $logged !== true}
    <Login />
  {:else}
    {#await user then}
      <Main />
    {/await}
  {/if}
{/await}
