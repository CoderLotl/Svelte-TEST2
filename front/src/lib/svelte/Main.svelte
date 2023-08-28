<script>
  import { logged, user } from "../js/stores.js";
  import { logout } from "../js/utilities/login.js";
  import { params } from "../js/init.js";
  import "../../assets/css/lobby.css";
  import logo from "../../assets/placeholder.png";
  import gears from "../../assets/settings-gears.png";
  import logout_icon from "../../assets/sign-out.png";

  async function logout_() {
    try {
      const response = await logout(params["home"] + "/auth/logout");

      if (response) {
        logged.set(false);
      }
    } catch (error) {}
  }
</script>

<div class="container">
  <input type="checkbox" id="menu-toggle" />
  <div class="menu-toggle-wrapper">
    <img class="logo" id="logo2" src={logo} alt="logo" />
    <label for="menu-toggle" class="burger-icon">&#9776;</label>
  </div>
  <header id="header">
    <img class="logo" id="logo1" src={logo} alt="logo2" />
    <span id="user">USER: {$user}</span>
    <nav class="nav-bar">
      <ul class="menu">
        <li>
          <a href="javascript:void(0);">
            <img src={gears} alt="gears" />
            Account
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" on:click={logout_}>
            <img src={logout_icon} alt="exit icon" />
            Logout
          </a>
        </li>
      </ul>
    </nav>
  </header>
  <div id="title-div">
    <span class="title">Characters List</span>
  </div>
</div>
