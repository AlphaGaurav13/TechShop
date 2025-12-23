pipeline {
    agent any

    environment {
        DOCKER_IMAGE = "gaurious/techshop"
        DOCKER_TAG   = "latest"
        DOCKER_CREDS = "dockerhub-creds"
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Deploy to XAMPP') {
            steps {
                bat '''
                echo Deploying to XAMPP...
                if not exist C:\\xampp\\htdocs\\techshop (
                    mkdir C:\\xampp\\htdocs\\techshop
                )
                xcopy /E /I /Y * C:\\xampp\\htdocs\\techshop
                '''
            }
        }

        stage('Build Docker Image') {
            steps {
                bat """
                docker build -t %DOCKER_IMAGE%:%DOCKER_TAG% .
                """
            }
        }

        stage('Login to Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: DOCKER_CREDS,
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    bat """
                    docker login -u %DOCKER_USER% -p %DOCKER_PASS%
                    """
                }
            }
        }

        stage('Push Image to Docker Hub') {
            steps {
                bat """
                docker push %DOCKER_IMAGE%:%DOCKER_TAG%
                """
            }
        }

        stage('Run App Container') {
            steps {
                bat """
                docker stop techshop_web 2>NUL || echo Container not running
                docker rm techshop_web 2>NUL || echo Container not found

                docker run -d ^
                  -p 8090:80 ^
                  --name techshop_web ^
                  %DOCKER_IMAGE%:%DOCKER_TAG%
                """
            }
        }
    }
}
