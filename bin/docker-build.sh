#!/usr/bin/env bash

set -o errexit -o errtrace -o nounset -o pipefail

: readonly "${DOCKER:=docker}"
: readonly "${GIT:=git}"
: readonly "${ERROR_OUTPUT:=/dev/stderr}"

: readonly -i "${EXIT_COULD_NOT_FIND_FILE:=70}"
: readonly -i "${EXIT_COULD_NOT_FIND_DIRECTORY:=75}"
: readonly -i "${EXIT_COULD_NOT_CREATE:=83}"

# ==============================================================================
# This script builds a docker container from the given Dockerfile, tag name and
# image-id file.
#
# Example usage:
#
#       docker-build /path/to/Dockerfile 'v0.1.0' /path/to/image-id-file.iid
# ==============================================================================
docker-build() {
    # ==========================================================================
    # Set variables
    # --------------------------------------------------------------------------
    local -r sDockerfilePath="${1?Two parameters required: <dockerfile-path> <tag-name> [image-id-file]}"
    local -r sTagName="${2?Two parameters required: <dockerfile-path> <tag-name> [image-id-file]}"
    local sIdFile="${3:-}"
    # --------------------------------------------------------------------------
    local -r sContextPath="$(dirname "${sDockerfilePath}")"
    # ==========================================================================

    # ==========================================================================
    # Validation
    # --------------------------------------------------------------------------
    if [[ ! -f "${sDockerfilePath}" ]];then
        echo " ! ERROR: Could not find given Dockerfile '${sDockerfilePath}'" >> "${ERROR_OUTPUT}"
        exit "${EXIT_COULD_NOT_FIND_FILE}"
    elif [[ "${sIdFile}" != "" ]] && [[ ! -f "${sIdFile}" ]]; then
        echo " ! ERROR: Could not find given Image ID file '${sIdFile}'" >> "${ERROR_OUTPUT}"
        exit "${EXIT_COULD_NOT_FIND_FILE}"
    else
        if [[ "${sIdFile}" = "" ]];then
            sIdFile="/tmp/docker.$(date +'%Y%m%d%H%M%S').iid"
            readonly sIdFile
        fi

        if [[ ! -f ${sIdFile} ]]; then
            touch "${sIdFile}"
        fi

        # ======================================================================
        # Build the run command
        # ----------------------------------------------------------------------
        local -a aCommand=("${DOCKER}" 'build')

        if [[ "${sIdFile}" != "" ]]; then
            aCommand+=('--iidfile' "${sIdFile}")
        fi

        local -r sBuildDate="$(date +'%Y%m%d%H%M%S')"

        aCommand+=(
            "--build-arg=PROJECT_PATH=${sContextPath}"
            '--file' "${sDockerfilePath}"
            '--label' "build_date=${sBuildDate}"
            '--label' 'maintainer=Ben Peachey <potherca@gmail.com>'
            '--label' "org.label-schema.build-date=${sBuildDate}"
            '--label' 'org.label-schema.description="Example OAuth2 implementation using the PHP League OAuth2 Server and Client packages"'
            '--label' 'org.label-schema.name="pdsinterop/example-oauth-server"'
            '--label' 'org.label-schema.schema-version="1.0"'
            '--label' 'org.label-schema.url="https://pdsinterop.org/example-oauth-server/"'
            '--label' 'org.label-schema.vcs-url="https://github.com/pdsinterop/example-oauth-server/"'
            '--label' 'org.label-schema.vendor="PDS Interop"'
            '--label' "org.label-schema.version=${sTagName}"
            '--label' "version=${sTagName}"
            '--tag' "pdsinterop/example-oauth-server:${sTagName}"
            "${sContextPath}"
        )

        # ======================================================================
        # Run the command
        # --------------------------------------------------------------------------
        "${aCommand[@]}"
    fi
}

if [[ "${BASH_SOURCE[0]}" != "$0" ]]; then
  export -f docker-build
else
  docker-build "${@}"
  exit $?
fi

#EOF
