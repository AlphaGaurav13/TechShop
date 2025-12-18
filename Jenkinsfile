pipeline {
    agent any

    environment {
        DOCKER_IMAGE = "alphagaurav/techshop"
        DOCKER_TAG = "latest"
        DOCKER_CREDS = "dockerhub-creds"
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
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

        stage('Run App Container') {
            steps {
                bat '''
                docker stop techshop_web || exit 0
                docker rm techshop_web || exit 0
                docker run -d ^
                  -p 8090:80 ^
                  --add-host=host.docker.internal:host-gateway ^
                  --name techshop_web ^
                  alphagaurav/techshop:latest
                '''
            }
        }
    }
}
