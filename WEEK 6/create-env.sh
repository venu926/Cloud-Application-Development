#!/bin/bash
#Coded by: Abdullah Suhail
#ITMO 544: Cloud Computing Technologies
#Mini Project #1 create-env Script

cd ~

cat > install-backend-env.txt <<EOF
#!/bin/bash
mkdir /tmp/edited
mkdir /tmp/images
cd /tmp
sudo chown -R ubuntu edited
sudo chown -R ubuntu images
cd ~
sudo git clone https://$5:$6@github.com/illinoistech-itm/asuhail.git
cd asuhail/ITMO-544
sudo mv asuhail_mp3/ /var/www/html
cd /var/www/html/asuhail_mp3
sudo chmod u+x ./polling.sh
sudo ./polling.sh &
sudo service apache2 restart
EOF

cat > install-app-env.txt <<EOF
#!/bin/bash
cd ~
sudo git clone https://$5:$6@github.com/illinoistech-itm/asuhail.git
cd asuhail/ITMO-544
sudo mv asuhail_mp3/ /var/www/html
cd /var/www/html
sudo service apache2 restart
EOF

#aws ec2 create-key-pair --key-name itmo544-key2 > itmo544-key2.priv

#secgroupid=`aws ec2 create-security-group --description "AWSCLI group" --group-name itmo544-group`
#echo $secgroupid

#aws ec2 authorize-security-group-ingress --group-name itmo544-group --protocol tcp --port 22 --cidr 0.0.0.0/0
#aws ec2 authorize-security-group-ingress --group-name $4 --protocol tcp --port 4000 --cidr 0.0.0.0/0

#sleep 15



aws elb create-load-balancer --load-balancer-name itmo544lb --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --security-groups $1 --availability-zones us-east-2a
aws elb create-app-cookie-stickiness-policy --load-balancer-name itmo544lb --policy-name elbpolicy --cookie-name cookie
aws elb set-load-balancer-policies-of-listener --load-balancer-name itmo544lb --load-balancer-port 80 --policy-names elbpolicy
aws elb describe-load-balancers --load-balancer-name itmo544lb

aws autoscaling create-launch-configuration --launch-configuration-name ec2launchconfig --key-name $2 --image-id ami-15725b70 --instance-type t2.micro --iam-instance-profile $3 --user-data file://install-app-env.txt

aws autoscaling create-auto-scaling-group --auto-scaling-group-name itmo544asg --launch-configuration-name ec2launchconfig --min-size 1 --max-size 5 --desired-capacity 3 --availability-zones us-east-2a --load-balancer-names itmo544lb --health-check-type ELB --health-check-grace-period 120

aws ec2 run-instances --image-id ami-15725b70 --count 1 --instance-type t2.micro --key-name $2 --iam-instance-profile Name=$3 --security-groups $4 --monitoring Enabled=true --user-data file://install-backend-env.txt

aws rds create-db-instance --db-instance-identifier itmo544rds --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username myawsuser --master-user-password myawsuser

aws rds wait db-instance-available --db-instance-identifier itmo544rds

aws rds create-db-instance-read-replica --db-instance-identifier itmo544rds-rr --source-db-instance-identifier itmo544rds --db-instance-class db.t2.micro --availability-zone us-east-2a

aws rds wait db-instance-available --db-instance-identifier itmo544rds-rr


endpoint=`aws rds describe-db-instances --output text | awk '{print $2 }' | grep -m 1 "itmo544rds."`
echo $endpoint

mysql -h $endpoint -P 3306 -u myawsuser -pmyawsuser <<'EOF'
create database itmo544db;
use itmo544db;
create table records
(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
email varchar(50),
phone varchar(50),
s3kachaurl varchar(100),
s3cookedurl varchar(100),
keyname varchar(100),
status int(1),
uuid varchar(100)
);
EOF
