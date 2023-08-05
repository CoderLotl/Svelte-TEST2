<script>
  import { user } from "../js/stores.js";
  import frogLogo from "../../assets/frog.gif";
  import '../../assets/css/Main.css';
  import cookieManager from "../js/utilities/cookieManager.js";

  function logout()
  {
    let array = $user;
    let manager = new cookieManager();
    array[2] = 0;

    manager.createCookie('SESSION',array, 1);
    user.set(array);
  }
</script>

<div>
  <img id="frog" src={frogLogo} alt="dancing frog" />
    <button on:click={logout}>
        Log Out
    </button>
</div>

