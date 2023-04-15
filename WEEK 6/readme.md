This is an elastic scaling application that can be deployed on the AWS cloud platform. Its main funtion is to accept a large number of photos and put them through a PHP image filter. When the application is deployed, it uses auto scaling groups, load balancers, Amazon RDS, Amazon S3 storage, Amazon CloudWatch, Amazon Simple Notification Service, Amazon Simple Queue Service, and custom configured AMIs.
Scripts/Additional Information
create-env.sh usage
You must pass 6 parameters to the script, include them in this order: security-group id, key-name, IAM instance profile, security-group name, github account username, github account password
destroy-env.sh usage
This script destroys created load balancers, launch configs, auto-scaling groups, S3 bucket, and created DB instances.
Assumptions/Clarifications
There is a cloudwatch php script included in this repo. After running the project, you can simply run the script and it will display the top 3 Datapoints from the EC2 Cloudwatch Metrics Array as well as the number of jobs processed and not processed via the status fields in the DB. Also, before running the application, make sure to unzip the vendor.zip file in the vendor folder.
