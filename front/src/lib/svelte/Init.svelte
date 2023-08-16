<script>
  import { onMount } from "svelte";
  import { params } from '../js/init.js';
  import { logged } from '../js/stores.js';  

  onMount( async () => {
    const response = await fetch(params['home'] + '/validate',
    {
      method: 'GET',
      credentials: 'include'
    });
    if(response.status === 200)
    {
      logged.set(true);
    }
  });
</script>
