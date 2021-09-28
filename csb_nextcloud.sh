echo #### Collect Openshift 4 Parameters ####
read -sp 'Openshift 4 Login Token: ' oc4_token
echo
read -p 'Openshift 4 License Plate: ' license
read -p 'Openshift 4 License Env: ' env
#read -sp 'OCJ DB User Password: ' ocjPass

#configuration
nextcloudImageTag="22.1.1"
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
firstTimeNamespace="1"
customNextcloudImagestream="my-nextcloud"
customNginxImagestream="my-nginx"
ipWhitelist="*"


# Config Values for CSB
appname="csb-sft"
nextcloudName="${appname}-nextcloud"
cronUrl="http://${nextcloudName}:8080/cron.php"


echo
oc login --token=${oc4_token} --server=https://api.silver.devops.gov.bc.ca:6443

# One Time Only Namespace Permissions
if [ ${firstTimeNamespace} == "1" ]
then
  oc project ${toolsNs}
  #Grant access to tools namespace images to the deployment namespace
  oc process -f cross-namespace-image-puller.yaml -p LICENSE_PLATE=${license} -p ENV=${env} | oc4 create -f -
fi

oc project ${oc4Ns}

# One Time Only Namespace DB Deploy
if [ ${firstTimeNamespace} == "1" ]
then
  oc process -f mysql.yaml -p tools_namespace=${toolsNs} -p MYSQL_DATABASE=${nextcloudName} -p DB_VERSION=${databaseImageTag} | oc4 create -f -
  oc process -f mysql-backup.yaml -p tools_namespace=${toolsNs} | oc4 create -f -
fi

oc process -f nextcloud.yaml -p NEXTCLOUD_HOST=${appname}-${env}.apps.silver.devops.gov.bc.ca -p tools_namespace=${toolsNs} -p MYSQL_DATABASE=${nextcloudName} -p nextcloud_name=${nextcloudName} -p ip_whitelist=${ipWhitelist} -p CURL_URL=${cronUrl} NEXTCLOUD_IMAGE_TAG=${nextcloudImageTag} | oc4 create -f -

### MySQL DB Add second user there
## Wait for pod to be up
## RSH to pod
## Login to mysql
##CREATE DATABASE `ocj-sft-nextcloud`;
##USE `ocj-sft-nextcloud`;
##CREATE USER `ocjuser`@'%' IDENTIFIED BY '${ocjPass}';
##GRANT USAGE ON *.* TO `ocjuser`@`%`;
##GRANT ALL PRIVILEGES ON `ocj-sft-nextcloud`.* TO `ocjuser`@`%`;


# Config Values for OCJ
appname="ocj-sft"
nextcloudName="${appname}-nextcloud"
cronUrl="http://${nextcloudName}:8080/cron.php"
oc process -f nextcloud.yaml -p NEXTCLOUD_HOST=${appname}-${env}.apps.silver.devops.gov.bc.ca -p tools_namespace=${toolsNs} -p MYSQL_DATABASE=${nextcloudName} -p nextcloud_name=${nextcloudName} -p ip_whitelist=${ipWhitelist} -p CURL_URL=${cronUrl} NEXTCLOUD_IMAGE_TAG=${nextcloudImageTag} | oc4 create -f -
