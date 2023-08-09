export const fetchPlainText = async (path) =>
{
  try
  {
    const response = await fetch(path); // Assuming index.php is in the same domain
    const data = await response.text();
    if(response.status === 200 && data.trim() !== '')
    {
      return data;
    }
    else
    {
      return 'Error fetching data'; // Display an error message if the request fails or the data is empty
    }
  }
  catch (error)
  {
    return `EXCEPTION: ${error}`; // Display an error message if an exception occurs
  }
};

export const fetchPlainTextWithParams = async (data, path) =>
{
  try
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
    const payload = await response.text();
    if(response.status === 200 && payload.trim() !== '')
    {
      return payload;
    }
    else
    {
      return 'Error fetching data'; // Display an error message if the request fails or the data is empty
    }
  }
  catch (error)
  {
    return `EXCEPTION: ${error}`; // Display an error message if an exception occurs
  }
};

export const fetchJSONText = async (path) =>
{
  try
  {
    const response = await fetch(path); // Assuming index.php is in the same domain
    const data = await response.json();
    if(response.status === 200)
    {
      return data;
    }
    else
    {      
      return 'Error fetching data'; // Display an error message if the request fails or the data is empty
    }
  }
  catch (error)
  {    
    return `EXCEPTION: ${error}`; // Display an error message if an exception occurs
  }
}; 