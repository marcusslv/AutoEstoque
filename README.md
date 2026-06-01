# AutoEstoque

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

Repositório para estudo e experimentação de aplicações que combinam Inteligência Artificial com boas práticas de arquitetura de software: Domain-Driven Design (DDD) e Clean Architecture.

## Objetivo

Oferecer um repositório de referência e estudo onde conceitos de IA (ex.: modelos, automações, pipelines de dados) são aplicados no contexto de um sistema modular, seguindo DDD e Clean Architecture. O foco é aprender como estruturar código, responsabilidades e fluxos de dados de forma testável, escalável e alinhada ao domínio.

## O que contém

- Backend em Laravel com organização por módulos e camadas seguindo princípios de DDD e Clean Architecture.
- Documentação de casos de uso e sequência de implementação em `docs/` para guiar estudos e exemplos práticos.
- Configuração Docker para facilitar execução local e reprodução do ambiente.

## Estrutura principal

- `backend/` — código-fonte da aplicação (Laravel).
- `docs/` — análises, casos de uso e instruções de setup.
- `docker/` e `docker-compose.yml` — configuração para execução contendo PHP e Nginx.

Para mais detalhes do setup, veja: [docs/setup-backend-docker.md](docs/setup-backend-docker.md)

## Tecnologias e padrões

- PHP + Laravel
- Domain-Driven Design (DDD)
- Clean Architecture (separação de camadas, portas/adapters, casos de uso)
- Docker para ambiente de desenvolvimento
- Testes automatizados (ex.: PHPUnit)

## Como contribuir

Issues, correções e pequenos exemplos práticos são bem-vindos. Prefira PRs pequenos e focados, com descrição do uso e testes quando aplicável.

## Licença

Este projeto está licenciado sob a Licença MIT. Consulte o arquivo [LICENSE](LICENSE) para os termos completos.

---

Se quiser, posso também gerar um badge, um guia de execução passo-a-passo ou um exemplo mínimo de uso de IA integrado ao módulo `Catalog`.
