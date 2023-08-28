<script context="module">  
  import { params } from "../js/init.js";
  import { logged, user } from "../js/stores.js";

  export async function login(data, path){
    try {
      const response = await fetch(path, {
        method: "POST",
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });
      return response;
    } catch (error) {
      console.log(error);
    }
  };

  export async function logout(path){
    const response = await fetch(path, {
      method: "POST",
      credentials: "include",
    });
    if (response.status === 200) {
      return true;
    } else {
      return false;
    }
  };

  export async function auth(){
    try {
      const response = await fetch(params["home"] + "/auth/validate", {
        method: "GET",
        credentials: "include",
      });
      return response;
    } catch (error) {
      console.log(`EXCEPTION: ${error}`);
    }
  };
</script>
