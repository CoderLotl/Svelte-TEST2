<script>
  import { logged } from "../js/stores.js";
  import frogLogo from "../../assets/frog.gif";
  import '../../assets/css/Main.css';
  import cookieManager from "../js/classes/cookieManager.js";

  function logout()
  {
    logged.set(false);
  }
</script>
<nav class="nav">

</nav>
<div>
  <img id="frog" src={frogLogo} alt="dancing frog" />
    <button on:click={logout}>
        Log Out
    </button>
</div>

