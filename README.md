# Music Information Management System

![Project Preview]

This Java-based project is designed to manage music information using AWS services. It includes a Java program for extracting song details from a JSON file, loading data into DynamoDB, downloading and uploading images to an S3 bucket, and a web application for interacting with the music database using PHP and JavaScript.

## Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Technologies](#technologies)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Data Ingestion**: The Java program extracts song information from JSON files, including title, artist, year, web URL, and image URL, and loads it into DynamoDB.

- **Image Handling**: The program downloads artist images and uploads them to an S3 bucket, making them accessible for the web application.

- **Web Application**: The web application allows users to:
    - Create user accounts
    - Search for songs in the DynamoDB database
    - Add new songs
    - Remove songs

## Installation

1. **Clone the Repository**: Start by cloning this repository to your local machine.

    ```bash
    git clone https://github.com/yourusername/music-information-management.git
    cd music-information-management
    ```

2. **Java Program Setup**: Set up the Java program by configuring your AWS credentials and endpoints. Make sure you have the AWS SDK for Java installed.

3. **Web Application Setup**:

    - For the PHP application, ensure you have a web server (e.g., Apache) set up, and configure the application to interact with the DynamoDB and S3 bucket.
    
    - For the JavaScript application, host it on a web server and ensure it can communicate with the Java program's APIs.

## Usage

1. **Running the Java Program**:

    - Run the Java program to load song information into DynamoDB.
    

2. **Start the Web Application**:

    - For the PHP application, access it via your web server's URL.
    
    - For the JavaScript application, open it in a web browser.

3. **Interact with the Web Application**:

    - Create an account, search for songs, and add or remove songs from the database.

## Technologies

- Java for backend data ingestion and management.
- AWS SDK for Java for AWS service integration (DynamoDB, S3).
- PHP for the web application's server-side functionality.
- JavaScript for the web application's client-side interactivity.

## Contributing

We welcome contributions from the community. If you'd like to contribute to this project, please follow the guidelines outlined in [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.