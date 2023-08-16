<script>
  import { logged } from "../js/stores.js";
  import { logout } from '../js/utilities/login.js';
  import { params } from '../js/init.js';
  import frogLogo from "../../assets/frog.gif";
  import '../../assets/css/Main.css';  

  async function logout_()
  {
    try
    {
      const response = await logout(params['home'] + '/auth/logout');

      if(response)
      {
        logged.set(false);
      }
    }
    catch(error)
    {
      
    }
  }
</script>
<nav class="nav">

</nav>
<div>
  <img id="frog" src={frogLogo} alt="dancing frog" />
    <button on:click={logout_}>
        Log Out
    </button>
</div>

