<script>  
  import { logged } from './lib/js/stores.js';
  import Init from './lib/svelte/Init.svelte';
  import Login from './lib/svelte/Login.svelte';
  import Main from './lib/svelte/Main.svelte';
  let loggedMain;

  logged.subscribe
  (
    (value) =>
    {
      loggedMain = value;
    }
  );
</script>

<Init />

{#if loggedMain === false}
  <Login />
{:else}
  <Main />
{/if}

