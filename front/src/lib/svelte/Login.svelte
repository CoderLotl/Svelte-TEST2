<script>
  //graphics
  import frogLogo from '../../assets/frog.gif';
  import '../../assets/css/Login.css';
  //store and params
  import { params } from '../js/init.js';
  import { logged, user } from '../js/stores.js';  
  //classes and functions  
  import { login } from '../js/utilities/login.js';
  import { fetchJSONText, fetchPlainText, fetchPlainTextWithParams } from '../js/utilities/fetch.js';

  
  let form = 
  {
    user: '',
    password: ''    
  };
  let message = '';

  const clear = () =>
  {
    message = '';
  }

  const login_ = async () =>
  {
    try
    {       
      const response = await login(form, params['home'] + '/auth/login');
      const data = await response.json();      
      if(response.status === 200)
      {
        message = 'YES';
        logged.set(true);
        user.set(data.user);
      }
      else
      {
        message = 'NO';
      }
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
        <input type="text" id="username" name="username" bind:value={form.user} required placeholder="Username"/>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" bind:value={form.password} required placeholder="Password"/>
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
</main>
