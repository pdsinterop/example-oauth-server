<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Enum\OAuth2;

/**
 * OAuth Authorization Request parameter
 *
 * Response Modes -- https://openid.net/specs/oauth-v2-multiple-response-types-1_0.html
 *
 * The Response Mode determines how the Authorization Server returns result
 * parameters from the Authorization Endpoint. Non-default modes are specified
 * using the response_mode request parameter.
 *
 * If response_mode is not present in a request, the default Response Mode
 * mechanism specified by the Response Type is used.
 *
 * The Response Mode request parameter response_mode informs the Authorization
 * Server of the mechanism to be used for returning Authorization Response
 * parameters from the Authorization Endpoint.
 *
 * Each Response Type value also defines a default Response Mode mechanism to be
 * used, if no Response Mode is specified using the request parameter.
 *
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * Informs the Authorization Server of the mechanism to be used for returning
 * Authorization Response parameters from the Authorization Endpoint.
 *
 * This use of this parameter is NOT RECOMMENDED with a value that specifies the
 * same Response Mode as the default Response Mode for the Response Type used.
 *
 * This specification defines the following Response Modes, which are described
 * with their response_mode parameter values:
 */
class ResponseMode
{
    /**
     * In this mode, Authorization Response parameters are encoded in the query
     * string added to the redirect_uri when redirecting back to the Client.
     */
    public const QUERY = 'query';

    /**
     * In this mode, Authorization Response parameters are encoded in the
     * fragment added to the redirect_uri when redirecting back to the Client.
     */
    public const FRAGMENT = 'fragment';
/*
 * For purposes of this specification, the default Response Mode for the OAuth
 * 2.0 code Response Type is the query encoding. For purposes of this
 * specification, the default Response Mode for the OAuth 2.0 token Response
 * Type is the fragment encoding.
 *
 * See OAuth 2.0 Form Post Response Mode [OAuth.Post] for an example of a
 * specification that defines an additional Response Mode. Note that it is
 * expected that additional Response Modes may be defined by other
 * specifications in the future, including possibly ones utilizing the HTML5
 * postMessage API and Cross Origin Resource Sharing (CORS).
 *
 * When a multiple-valued Response Type is defined, it is RECOMMENDED that the
 * following encoding rules be applied for the issued response from the
 * Authorization Endpoint.
 *
 * All parameters returned from the Authorization Endpoint SHOULD use the same
 * Response Mode. This recommendation applies to both success and error responses.
 *
 * Rationale: This significantly simplifies Client parameter processing. It also
 * can have positive performance benefits, as described below.
 *
 * For instance, if a response includes fragment encoded parts, a User Agent
 * Client component must be involved to complete processing of the response. If
 * a new query parameter is added to the Client URI, it will cause the User
 * Agent to re-fetch the Client URI, causing discontinuity of operation of the
 * User Agent based Client components. If only fragment encoding is used, the
 * User Agent will simply reactivate the Client component, which can then
 * process the fragment and also convey any parameters to a Client host as
 * necessary, e.g., via XmlHttpRequest. Therefore, full fragment encoding always
 * results in lower latency for response processing.
 */
}
