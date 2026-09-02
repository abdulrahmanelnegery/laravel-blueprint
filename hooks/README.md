# Git hooks

`pre-commit` runs the same checks as CI: Pint (formatting), PHPStan (level 8),
and Pest. It is kept here, in version control, rather than in `.git/hooks`
(which git does not track).

## Enable

```sh
ln -sf ../../hooks/pre-commit .git/hooks/pre-commit
```

The path is relative to `.git/hooks`, so the symlink keeps working after a
fresh clone as long as you run the command again.

## Bypass

```sh
git commit --no-verify
```
