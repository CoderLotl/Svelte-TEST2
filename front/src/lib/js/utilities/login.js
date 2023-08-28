export const login = async (data, path) => {
  try
  {
    const response = await fetch(path, {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });
    if (response.status === 200) {
      return true;
    } else {
      return false;
    }
  }
  catch(error)
  {
    console.log(error);
  }
};

export const logout = async (path) => {
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
