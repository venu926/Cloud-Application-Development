#!/bin/bash
#Coded by: Abdullah Suhail
#ITMO 544: Cloud Computing Technologies
#Mini Project #1 destroy-env Script


insid1=`aws ec2 describe-instance-status --output text | awk 'FNR == 1 {print $3 }' | grep "i-"`
#echo $insid1

#insid2=`aws ec2 describe-instance-status --output text | awk 'FNR == 7 {print $3 }' | grep "i-"`
#echo $insid2

aws ec2 terminate-instances --instance-id $insid1
#aws ec2 terminate-instances --instance-id $insid2

aws elb delete-load-balancer --load-balancer-name itmo544lb
aws autoscaling delete-auto-scaling-group --auto-scaling-group-name itmo544asg --force
aws autoscaling delete-launch-configuration --launch-configuration-name ec2launchconfig
aws rds delete-db-instance --db-instance-identifier itmo544rds --skip-final-snapshot
aws rds delete-db-instance --db-instance-identifier itmo544rds-rr --skip-final-snapshot
aws s3 rm s3://s3kacha --recursive
aws s3 rm s3://s3cooked --recursive
aws s3 rb s3://s3kacha --force 
aws s3 rb s3://s3cooked --force 
insids=`aws ec2 describe-instances --output table | awk '{print  $4 }' | grep "^i-"`
echo $insids
for i in "${insids[@]}"
do
aws ec2 terminate-instances --instance-ids $insids
done
