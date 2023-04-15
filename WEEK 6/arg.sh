#!/bin/bash

host=`aws rds describe-db-instances --output text | awk '{print $2 }' | grep "rds.amazonaws.com"`
echo $host

export endpoint=$host
echo $endpoint
