# Use the latest Ubuntu image as the base
FROM ubuntu:latest

COPY dist/getfunci.py /home/ubuntu/app/getfunci.py

# Set the working directory
WORKDIR /home/ubuntu/app

# Update the package list and install Python and pip
RUN apt-get update && apt-get install -y python3 python3-pip

# Install the required libraries from getfunci.py
RUN pip3 install requests beautifulsoup4 openpyxl flask --break-system-packages

# Keep the container running
CMD ["python3", "getfunci.py"]