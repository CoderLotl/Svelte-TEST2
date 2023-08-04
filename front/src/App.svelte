<script>  
  import { user } from './lib/js/stores.js';
  import Init from './lib/svelte/Init.svelte';
  import Login from './lib/svelte/Login.svelte';
  import Main from './lib/svelte/Main.svelte';
  let userMain;

  user.subscribe
  (
    (value) =>
    {
      userMain = value;
    }
  );
</script>

<Init />

{#if userMain[2] === 0}
  <Login />
{:else}
  <Main />
{/if}

