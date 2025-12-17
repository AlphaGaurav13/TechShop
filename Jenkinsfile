pipeline {
    agent any

    stages {

        stage('Clone Repo') {
            steps {
                git 'https://github.com/AlphaGaurav13/TechShop.git'
            }
        }

        stage('Build Docker Image') {
            steps {
                sh 'docker build -t techshop:latest .'
            }
        }

        stage('Run Container') {
            steps {
                sh '''
                docker stop techshop || true
                docker rm techshop || true
                docker run -d -p 8080:80 --name techshop techshop:latest
                '''
            }
        }
    }
}
