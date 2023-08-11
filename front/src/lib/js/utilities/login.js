export const login = async (data, path) =>
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
        let result = await response.json();
        return result[0];
    }      
    else
    {
        return false;
    }
};