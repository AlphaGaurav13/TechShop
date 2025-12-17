pipeline {
    agent any

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                bat 'docker build -t techshop:latest .'
            }
        }

        stage('Run App Container') {
            steps {
                bat '''
                docker stop techshop_web || exit 0
                docker rm techshop_web || exit 0
                docker run -d ^
                  -p 8080:80 ^
                  --add-host=host.docker.internal:host-gateway ^
                  --name techshop_web ^
                  techshop:latest
                '''
            }
        }
    }
}
