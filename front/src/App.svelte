<script>
  import { auth } from "./lib/svelte/Init.svelte";
  import Login from "./lib/svelte/Login.svelte";
  import Main from "./lib/svelte/Main.svelte";
  import { params } from "./lib/js/init.js";
  import { logged, user } from "./lib/js/stores.js";

  auth();
</script>

{#await logged then}
  {#if $logged === false}
    <Login />
  {:else}
    {#await user then}
      <Main />
    {/await}
  {/if}
{/await}
