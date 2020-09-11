# OAuth Client

## Flow

There are four Actors in the little dance otherwise know as the OAuth2 HTTP
Handshake.

- Authorization Server
- Client Application
- Resource Server
- User (a.k.a Resource Owner)

Any HTTP redirect URIs must be served via HTTPS

OAuth 2 provides several "grant types" for different use cases. The grant types defined are:

- **Authorization code** for apps running on a web server, and browser-based and 
  mobile apps using the Proof Key for Code Exchange (PKCE) technique.
- **Client credentials** for application access without a user present, machine-to-machine communication.
- **Password credentials** for logging in with a username and password (only for first-party apps).

## Generic flow

1. An Application Client requests a resource from a Resource Server
2. The Resource Server requires an access token
3. If the request contains a token the resource is return
4. If the token is missing, a redirect to an auth server is made to request one

Once the client has a token, it can just make requests for resources until the
token expires. At such a time, a new request will need to be made to refresh the
token.  

The flow to retrieve a token goes like this:

1. The Application (Client) asks for authorization from the Resource Owner in order to access the resources.

2. Provided that the Resource Owner authorizes this access, the Application receives an Authorization Grant. This is a credential representing the Resource Owner's authorization.

3. The Application requests an Access Token by authenticating with the Authorization Server and giving the Authorization Grant.

4. Provided that the Application is successfully authenticated and the Authorization Grant is valid, the Authorization Server issues an Access Token and sends it to the Application.

5. The Application requests access to the protected resource by the Resource Server, and authenticates by presenting the Access Token.

6. Provided that the Access Token is valid, the Resource Server serves the Application's request.

- - - - 
1. Resource request (without Access token) from Resource server
2. Redirect from Resource server to Auth Server for an authorization request 
2. Redirect from Auth Server back to Resource server with authorization grant 
4. Authorization grant request
5. Authorization grant response
6. Resource request (with Access token)
7. Resource response

## Specific flows

### Authorization Code

There are several things that happen in order:

1. A **User** visits a page on the **Client Application** that triggers the whole dance<br/> 
   _This is usually a login request. In this application it is: `/client/login`_<br/>

2. The **Client Application** redirect the **User** to a **Authorization Server**<br/>
   _This is an authorization request. In this application it goes to `/oauth2/authorize`_<br/>
   The URL MUST contain a `client_id` (to identify the client application) and `response_type` (in this case `code`).
   It MAY also contain: 
    - `redirect_uri`  A successful response from this endpoint results in a redirect to this URL.
    - `scope`  A space-delimited list of permissions that the application requires.
    - `state`  If set, it is returned to the client application in the `redirect_uri`.

3.

4.

Authorization request
Authorization grant
Access Token
Resource

'redirectUri' => $host . '/your-redirect-url/',
'urlAccessToken' => $host . '/oauth2/token',
'urlResourceOwnerDetails' => $host . '/oauth2/resource_owner.txt',
  
### Client Credentials

### Password

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

The server response consists of a URL route and the code behind it.

Although the routing is application specific, the response code is not.

Parts of what the response code need, however, _are_ application specific.
For instance Entities/Models and the Repositories that provide them.

This project provides data classes that can be filled by the providing
application.

Rather than having to change existing classes, the integrating application will
need to implement an adapter for each model to convert from and to their own
models.

To make this easier, Factories are provided for each entity that are used by the
repositories.

To prove things work, a client is also provided. This is _only_ meant to be used
in the development and testing stages of an application.

**!!! IT IS NOT  MEANT OT BE USED IN PRODUCTION !!!**

No guarantees regarding safety or privacy are given if you do.
