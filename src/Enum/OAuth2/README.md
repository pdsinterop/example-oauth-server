# OAuth2 enums

This directory contains PHP enum classes related to the OAuth 2.0 Authorization 
Framework as defined in [RFC 6749](https://tools.ietf.org/html/rfc6749)

Currently, there are enum classes for:

- Grant types
- OAuth parameters
- Meta data

## Grant types

At the time of this writing, four grant types are safe to use:

- Authorization Code (with PKCE)
- Client Credentials
- Device Code
- Refresh Token

The following grant types are deprecated and SHOULD NOT be used, as they are no
longer considered secure:

- Authorization Code without PKCE
- Implicit Flow
- Password Grant


## OAuth parameters

Information that is used as GET or POST parameter.

The following lists which classes in contain values from which registry:

- `AccessTokenType`: OAuth Access Token Types
- `Parameter`: OAuth Parameters
- `ResponseType`: OAuth Authorization Endpoint Response Types

The names and values of the oauth-parameters in the enum classes in are taken 
from [the OAuth Parameters registry at IANA](https://www.iana.org/assignments/oauth-parameters/oauth-parameters.xhtml)

## Meta data

Information that is NOT part of GET or POST parameters but used elsewhere (like in `/.well-known/` documents such as `/.well-known/oauth-authorization-server` or `/.well-known/openid-configuration`).

- `MetaData\AuthorizationServer`: OAuth 2.0 Authorization Server Metadata (RFC8414)
- `MetaData\DynamicClientRegistration`: OAuth 2.0 Dynamic Client Registration Protocol (RFC7591)

Values have been taken from their respective RFCs.

<!--
- OAuth Extensions Error Registry
- OAuth Token Endpoint Authentication Methods
- OAuth Token Introspection Response
- OAuth Token Type Hints
- OAuth URI
- PKCE Code Challenge Methods
-->
