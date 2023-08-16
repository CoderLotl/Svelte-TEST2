export const login = async (data, path) => {
    const response = await fetch(path,
        {
            method: 'POST',
            credentials: 'include',
            headers:
            {
            'Content-Type': 'application/json'
            },
            body: JSON.stringify(data),
        }
    );
    if (response.status === 200)
    {        
        return true;
    }      
    else
    {
        return false;
    }
};

export const logout = async (path) => {
    const response = await fetch(path,
        {
            method: 'POST',
            credentials: 'include',
        }
    );
    if(response.status === 200)
    {
        return true;
    }
    else
    {
        return false;
    }
}