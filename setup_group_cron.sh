echo #### Collect Openshift 4 Parameters ####
read -sp 'Openshift 4 Login Token: ' oc4_token
echo
read -p 'Openshift 4 License Plate: ' license
read -p 'Openshift 4 License Env: ' env
read -p 'Reverse Proxy Address: ' reverseProxyAddr
#read -sp 'OCJ DB User Password: ' ocjPass

#configuration
nextcloudImageTag="22.1.1-fpm"
nextcloudImage="nextcloud:${nextcloudImageTag}"
nextcloudImagestreamName="nextcloud"
nginxImageTag="latest"
nginxImage="nginx:${nginxImageTag}"
nginxImagestreamName="nginx"
databaseImageTag="latest"
databaseImage="mysql:${databaseImageTag}"
databaseImagestreamName="mysql"
oc4Ns="${license}-${env}"
toolsNs="${license}-tools"

customNextcloudImagestream="my-nextcloud"
customNginxImagestream="my-nginx"



# Config Values for CSB
appname="csb-sft"
nextcloudName="${appname}-nextcloud"
oc project ${oc4Ns}
oc process -f nextcloud-groupcron.yaml -p tools_namespace=${toolsNs} -p REVERSE_PROXY_ADDR=${reverseProxyAddr} -p MYSQL_DATABASE=${nextcloudName} -p nextcloud_name=${nextcloudName} -p NEXTCLOUD_IMAGE_TAG=${nextcloudImageTag} | oc4 create -f -
