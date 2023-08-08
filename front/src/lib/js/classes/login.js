class Login
{
    login =  async (data, path) =>
    {
        const response = await fetch(path,
            {
                method: 'POST',
                headers:
                {
                'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }
        );
        if (response.status === 200)
        {
            return await response.json();
        }      
        else
        {
            return false;
        }
    }
}

export default Login;