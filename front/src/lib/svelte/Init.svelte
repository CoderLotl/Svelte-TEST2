<script>
  import { onMount } from "svelte";
  import { logged } from '../js/stores.js';  

  onMount( async () => {
    const response = await fetch('http://localhost/validate',
    {
      method: 'GET'
    });
    if(response.status === 200)
    {
      logged.set(true);
    }
  });
</script>
