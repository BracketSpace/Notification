# How to Contribute

Thank you for taking the time to contribute to our project. We're thrilled to share our work with you and hope you'll enjoy sharing your work with us! Here's a bunch of useful information for you to start.

## Gitflow

We use [Gitflow](https://danielkummer.github.io/git-flow-cheatsheet/) on each of our projects. According to the basic assumptions of this workflow, here are the steps that you need to take when developing a new feature:

1. Create a new branch from `develop`
2. Work on this branch until your feature is finished
3. Rebase your branch onto current `develop`
4. Create a Pull Request and wait for the Code Review
5. Iterate with all needed changes and fixes
6. Enjoy your branch being merged once accepted

### Branch names

Typically, branches are named after the developed feature, i.e. `feature/Name-of-the-Feature` and so we do.

## Conventional Commits

We're following the rule of [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/).

A well-formed git commit description line should always be able to complete the following sentence:
> When applied, this commit should *\<your description line here\>*

## Pull Requests

When you're done with developing your feature, you should create a [Pull Request](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-pull-requests) and wait for your code to be reviewed. Once all the changes and fixes will be applied, the PR will be merged into the develop branch.

### PR size

Create PRs as small as they can possibly be. Create as much of them as you need. This makes them easier to review and less likely to cause serious merge conflicts.

## Changelog

We appreciate it if you keep the changelog for every change.

Changelog usually lives in `readme.txt` files when it comes to a plugin, or it is in `CHANGELOG.md`. New sections (unreleased changes) can be marked as `[Next]`. It will be changed to the next version number when a new version is released.

***

And that's it for the basic information you need. Thanks!
