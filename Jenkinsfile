pipeline {
    agent any

    stages {

        stage('Build Docker Image') {
            steps {
                bat 'docker build -t techshop:latest .'
            }
        }

        stage('Run Container') {
            steps {
                bat '''
                docker stop techshop || exit 0
                docker rm techshop || exit 0
                docker run -d -p 8080:80 --name techshop techshop:latest
                '''
            }
        }
    }
}
