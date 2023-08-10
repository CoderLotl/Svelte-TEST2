<script>
  //graphics
  import frogLogo from '../../assets/frog.gif';
  import '../../assets/css/Login.css';
  //store and params
  import { params } from '../js/init.js';
  import { user } from '../js/stores.js';  
  //classes and functions
  import cookieManager from "../js/classes/cookieManager.js";
  import {login} from '../js/utilities/login.js';
  import { fetchJSONText, fetchPlainText, fetchPlainTextWithParams } from '../js/utilities/fetch.js';

  let form = 
  {
    user: '',
    password: '',
    isForm: true
  };
  let message = '';

  function loginTest()
  {
    let array = $user;
    let manager = new cookieManager();
    array[2] = 1;

    manager.createCookie('SESSION', array, 1);
    user.set(array);
  }

  const clear = () =>
  {
    message = '';
  }

  const login_ = async () =>
  {
    try
    {       
      //const result = login(form, params['home'] + '/login');
      
      message = await fetchPlainTextWithParams(form, params['home'] + '/login');

      /*if(result)
      {
        result.push(1);
        let manager = new cookieManager();
        manager.createCookie('SESSION', result, 1);

        user.set(result);
      }*/
    }
    catch (error)
    {
      message = `EXCEPTION: ${error}`; // Display an error message if an exception occurs
    }
  };

  const fetchData = async () =>
  {
    message = await fetchPlainText(params['home'] + '/db');
    //message = await fetchJSONText(params['home'] + '/json');
  };
</script>

<main>
  <div>
    <img src={frogLogo} class="logo frog" alt="dancing frog" />
  </div>
  <div class="login-container">
    <h1>Your Game Name</h1>
    <form on:submit|preventDefault={ login_ } name="login-form" class="login-form" method="post">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" bind:value={form.user} required />
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" bind:value={form.password} required />
      </div>
      <div class="form-group">
        <input type="submit" value="Log In" />
      </div>
    </form>
    <button on:click={fetchData}>
      Press the button to test the server.
    </button>
    <button on:click={clear}>
      Clear
    </button>
    <br>
    <p>
      {message}
    </p>
    <div class="game-features">
      <p>Features:</p>
      <ul>
        <li>Feature 1</li>
        <li>Feature 2</li>
        <li>Feature 3</li>
        <!-- Add more features relevant to your game -->
      </ul>
    </div>
  </div>
  <div class='log'>
    <button on:click={loginTest}>
      TEST LOG
    </button>
  </div>
</main>
