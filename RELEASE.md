# Release instructions

## Phast

We follow semantic versioning. Did you only fix bugs, then increment the
patchlevel. Did you also add any new features, then increment the minor version
and reset the patchlevel. In case of breaking changes we will increment the
major version.

Follow these steps to release a new version of Phast:

1. Update the change log.
    1. Make sure all important changes since the last release are mentioned.
    1. Make sure the header links are set. (Version numbers link to GitHub log.)
1. Commit changelog.
1. Tag release: `git tag 1.5.0`
1. Push commits and tags: `git push --tags origin master`
1. Release PhastPress.

## PhastPress

PhastPress versions follow those of Phast. If a change is made to PhastPress
without an accompanying update of Phast itself, a letter is used to indicate the
change. Eg, 1.5.1 → 1.5.1a.
