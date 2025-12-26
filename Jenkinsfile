pipeline {
    agent any

    environment {
        DOCKER_IMAGE = "gaurious/techshop"
        DOCKER_TAG   = "latest"
        DOCKER_CREDS = "dockerhub-creds"
        CONTAINER_NAME = "techshop_web"
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image (Same Tag)') {
            steps {
                bat "docker build -t %DOCKER_IMAGE%:%DOCKER_TAG% ."
            }
        }

        stage('Login to Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: DOCKER_CREDS,
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    bat "docker login -u %DOCKER_USER% -p %DOCKER_PASS%"
                }
            }
        }

        stage('Push Image to Docker Hub') {
            steps {
                bat "docker push %DOCKER_IMAGE%:%DOCKER_TAG%"
            }
        }

        stage('Restart Docker Container') {
            steps {
                bat """
                docker pull %DOCKER_IMAGE%:%DOCKER_TAG%
                docker stop %CONTAINER_NAME%
                docker rm %CONTAINER_NAME%
                docker run -d --name %CONTAINER_NAME% -p 8090:80 %DOCKER_IMAGE%:%DOCKER_TAG%
                """
            }
        }
    }
}
